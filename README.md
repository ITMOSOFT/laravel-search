# ITMOSOFT Laravel Search

Database-backed search indexing for Eloquent models in Laravel 12 and 13.

## Features
- Index explicitly selected fields from any Eloquent model.
- Polymorphic model indexing, including custom morph maps.
- Multi-term matching: all query terms must match.
- Configurable result limits and minimum query length.
- Re-index records, delete individual records and flush a model type.
- JSON payload stored alongside normalized searchable text.
- Configurable index table name and prefix.

## Requirements
- PHP 8.3+
- Laravel 12 or 13

## Installation
```bash
composer require itmosoft/laravel-search
php artisan vendor:publish --tag=search-config
php artisan migrate
```

## Make a model searchable
```php
use Illuminate\Database\Eloquent\Model;
use ItmoSoft\Search\Concerns\Searchable;

class Product extends Model
{
    use Searchable;
    protected array $searchableFields = ['name', 'description', 'sku'];
}
```
Only explicitly allowlisted fields are indexed by the default implementation. Override `toSearchableArray()` for computed or related values. Never index passwords, tokens, private notes, payment data or other sensitive fields.

## Index and search
```php
use ItmoSoft\Search\SearchManager;

$search = app(SearchManager::class);
$search->index($product); // Call after saving.
$products = $search->search('wireless keyboard', Product::class, limit: 25);
$search->delete($product);
$removed = $search->flush(Product::class);
```
This release does not automatically observe model saves/deletes. Call these methods from application observers or jobs.

## Configuration
- `table_prefix`: default `itmosoft_`
- `index_table`: optional explicit index table name
- `default_limit`: default `20`
- `maximum_limit`: default `100`
- `minimum_term_length`: default `2`

## Search behavior and limitations
This release uses database `LIKE` matching. It is suitable for modest catalogs and simple search needs, not a replacement for a dedicated search engine. It does not provide typo tolerance, stemming, relevance scoring, faceted filtering, language analyzers, synonyms or distributed indexing. Larger catalogs should use a dedicated engine through a future driver integration.

The `payload` column stores indexed data. Treat the search index as a data store with the same access-control and retention requirements as source data. Authorization and tenant scoping remain the host application's responsibility. Add tenant scoping to the schema and query path before using this package in a multi-tenant application.

## Testing
```bash
composer install
composer validate --strict
composer test
```

## License
MIT.
