# Changelog

## 1.0.2 - Unreleased

- Normalize search terms to lowercase before matching for consistent behavior across database collations.
- Escape SQL `LIKE` wildcard characters so `%` and `_` are searched literally.
- Reject indexing or deleting unsaved Eloquent models.
- Validate the searchable table name returned by `searchableAs()`.
- Add regression tests for case normalization, wildcard handling and unsaved models.

## 1.0.0 - 2026-11-14

- Initial release.
- Added explicit Eloquent model indexing and multi-term database search.
- Added index maintenance operations and Laravel 12/13 CI workflow.
