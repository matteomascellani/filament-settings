# filament-settings

Reusable Filament settings package.

## Goal

Provide a generic settings system with:

- `settings` table for setting definitions
- `setting_values` table for one or multiple values per setting
- optional polymorphic scope (`settable_type`, `settable_id`) for model-specific overrides

## Requirements

- PHP `^8.2`
- Laravel `^11`
- Filament `^4`

## Install (local path development)

In your Laravel project `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../filament-settings",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

Then:

```bash
composer require matteomascellani/filament-settings:*
```

## Publish

```bash
php artisan vendor:publish --tag=filament-settings-config
php artisan vendor:publish --tag=filament-settings-migrations
```

## Notes

This package currently provides the base persistence layer (config, models, migration, helpers).
Filament resources/pages will be added in the host app or in the next package iteration.
