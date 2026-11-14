# Release checklist

1. Run `composer validate --strict`.
2. Run `composer test` on PHP 8.3 with Laravel 12 and Laravel 13.
3. Ensure `.env`, `vendor/`, and cache files are not committed.
4. Commit and push `main`.
5. Create and push the annotated tag:
   ```bash
   git tag -a v1.0.0 -m "Initial release"
   git push origin v1.0.0
   ```
6. Create GitHub Release `v1.0.0 — Initial Release`.
7. Submit `https://github.com/ITMOSOFT/laravel-search` to Packagist and configure its GitHub webhook.
8. Verify with `composer require itmosoft/laravel-search`.

Do not publish until both Laravel matrix jobs pass.
