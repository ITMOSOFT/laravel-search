<?php
namespace ItmoSoft\Search\Tests\Unit;
use ItmoSoft\Search\SearchManager;
use ItmoSoft\Search\Tests\Support\Product;
use ItmoSoft\Search\Tests\TestCase;
class SearchManagerTest extends TestCase
{
    public function test_short_search_terms_return_empty_results(): void
    {
        $this->assertCount(0, app(SearchManager::class)->search('x', Product::class));
    }
}
