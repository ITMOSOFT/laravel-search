<?php

namespace ItmoSoft\Search\Concerns;

trait Searchable
{
    public function getSearchableFields(): array
    {
        return $this->searchableFields ?? [];
    }

    public function searchableAs(): string
    {
        return $this->getTable();
    }

    public function toSearchableArray(): array
    {
        return $this->only($this->searchableFields);
    }
}
