<?php
namespace ItmoSoft\Search\Tests\Feature;
use ItmoSoft\Search\SearchManager;
use ItmoSoft\Search\Tests\Support\Product;
use ItmoSoft\Search\Tests\TestCase;
class SearchManagerTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); $this->artisan('migrate')->run(); }
    public function test_indexed_product_can_be_found_by_multiple_terms(): void
    {
        $product = Product::create(['name' => 'Oak Writing Desk', 'description' => 'Solid wood office furniture', 'internal_secret' => 'private']);
        $manager = app(SearchManager::class);
        $manager->index($product);
        $results = $manager->search('oak office', Product::class);
        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($product));
    }
    public function test_index_can_be_updated_and_deleted(): void
    {
        $product = Product::create(['name' => 'Blue Mug', 'description' => 'Ceramic cup']);
        $manager = app(SearchManager::class);
        $manager->index($product);
        $product->name = 'Green Mug'; $product->save(); $manager->index($product);
        $this->assertCount(0, $manager->search('blue', Product::class));
        $this->assertCount(1, $manager->search('green', Product::class));
        $manager->delete($product);
        $this->assertCount(0, $manager->search('green', Product::class));
    }
    public function test_flush_removes_documents_for_model_type(): void
    {
        $product = Product::create(['name' => 'Desk']);
        $manager = app(SearchManager::class); $manager->index($product);
        $this->assertSame(1, $manager->flush(Product::class));
        $this->assertCount(0, $manager->search('desk', Product::class));
    }
}
