<?php
namespace ItmoSoft\Search\Tests;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use ItmoSoft\Search\SearchServiceProvider;
abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array { return [SearchServiceProvider::class]; }
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]);
        $app['config']->set('search.table_prefix', 'itmosoft_');
    }
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Schema::create('products', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->string('internal_secret')->nullable(); $table->timestamps();
        });
    }
}
