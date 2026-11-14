<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        $name = config('search.index_table') ?: config('search.table_prefix', 'itmosoft_').'search_documents';
        Schema::create($name, function (Blueprint $table): void {
            $table->id();
            $table->string('searchable_type');
            $table->string('searchable_id', 100);
            $table->string('searchable_table')->nullable();
            $table->longText('searchable_text');
            $table->json('payload')->nullable();
            $table->timestamp('indexed_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['searchable_type', 'searchable_id'], 'search_documents_model_unique');
            $table->index(['searchable_type', 'indexed_at']);
        });
    }
    public function down(): void
    {
        $name = config('search.index_table') ?: config('search.table_prefix', 'itmosoft_').'search_documents';
        Schema::dropIfExists($name);
    }
};
