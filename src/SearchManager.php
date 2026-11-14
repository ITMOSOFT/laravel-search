<?php
namespace ItmoSoft\Search;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SearchManager
{
    public function index(Model $model): void
    {
        if (! method_exists($model, 'toSearchableArray')) {
            throw new InvalidArgumentException('Use the Searchable trait or implement toSearchableArray().');
        }
        $data = $model->toSearchableArray();
        if (! is_array($data)) {
            throw new InvalidArgumentException('toSearchableArray() must return an array.');
        }
        DB::table($this->table())->updateOrInsert(
            ['searchable_type' => $model->getMorphClass(), 'searchable_id' => (string) $model->getKey()],
            [
                'searchable_table' => method_exists($model, 'searchableAs') ? $model->searchableAs() : $model->getTable(),
                'searchable_text' => mb_strtolower($this->flatten($data)),
                'payload' => json_encode($data, JSON_THROW_ON_ERROR),
                'indexed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function delete(Model $model): void
    {
        DB::table($this->table())
            ->where('searchable_type', $model->getMorphClass())
            ->where('searchable_id', (string) $model->getKey())
            ->delete();
    }

    /** @param class-string<Model> $modelClass */
    public function search(string $term, string $modelClass, ?int $limit = null): EloquentCollection
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException('The model class must extend Eloquent Model.');
        }
        $term = trim($term);
        if (mb_strlen($term) < (int) config('search.minimum_term_length', 2)) {
            return new EloquentCollection();
        }
        $prototype = new $modelClass();
        $query = DB::table($this->table())->where('searchable_type', $prototype->getMorphClass());
        foreach (preg_split('/\\s+/u', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $token) {
            $query->where('searchable_text', 'like', '%'.addcslashes($token, '\\%_').'%');
        }
        $take = max(1, min($limit ?? (int) config('search.default_limit', 20), (int) config('search.maximum_limit', 100)));
        $ids = $query->orderByDesc('indexed_at')->limit($take)->pluck('searchable_id')->all();
        if ($ids === []) {
            return new EloquentCollection();
        }
        $models = $modelClass::query()->whereKey($ids)->get()->keyBy(fn (Model $m) => (string) $m->getKey());
        return new EloquentCollection(collect($ids)->map(fn ($id) => $models->get((string) $id))->filter()->values()->all());
    }

    /** @param class-string<Model> $modelClass */
    public function flush(string $modelClass): int
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException('The model class must extend Eloquent Model.');
        }
        return DB::table($this->table())->where('searchable_type', (new $modelClass())->getMorphClass())->delete();
    }

    private function table(): string
    {
        return config('search.index_table') ?: config('search.table_prefix', 'itmosoft_').'search_documents';
    }

    private function flatten(array $data): string
    {
        $values = [];
        array_walk_recursive($data, function ($value) use (&$values): void {
            if (is_scalar($value) || $value instanceof \Stringable) {
                $value = trim((string) $value);
                if ($value !== '') $values[] = $value;
            }
        });
        return implode(' ', $values);
    }
}
