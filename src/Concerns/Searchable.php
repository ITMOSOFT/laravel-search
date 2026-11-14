<?php
namespace ItmoSoft\Search\Concerns;
trait Searchable
{
    /** @var array<int, string> Explicitly allowlisted fields to index. */
    protected array $searchableFields = [];
    public function searchableAs(): string
    {
        return $this->getTable();
    }
    public function toSearchableArray(): array
    {
        return $this->only($this->searchableFields);
    }
}
