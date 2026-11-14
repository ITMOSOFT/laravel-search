<?php

namespace ItmoSoft\Search\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use ItmoSoft\Search\Concerns\Searchable;

class Product extends Model
{
    use Searchable;

    protected $table = 'products';

    protected $guarded = [];

    protected array $searchableFields = [
        'name',
        'description',
    ];
}
