<?php
namespace ItmoSoft\Search;
use Illuminate\Support\ServiceProvider;
class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/search.php', 'search');
        $this->app->singleton(SearchManager::class);
        $this->app->alias(SearchManager::class, 'search');
    }
    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/search.php' => config_path('search.php')], 'search-config');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
