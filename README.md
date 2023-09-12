![Screenshot of Login](./arts/screenshot.png)

# Filament Translations

Manage your translation with DB and cache, you can scan your languages tags like `trans()`, `__()` from files, and get the string inside DB and translate them use UI. Also provides automatic, optional Google translation, language switcher and more.

## Screenshots

![Screenshot of list](./arts/list.png)
![Screenshot of edit](./arts/edit.png)
![Screenshot of switcher](./arts/switcher.png)

## Installation

You can install the package via composer:

```bash
composer require 3x1io/filament-translations
```

Run migration:

```bash
php artisan vendor:publish --tag="filament-translations"
php artisan vendor:publish --tag="filament-translations-config"
php artisan migrate
```

In `config/app.php` (Laravel) or `bootstrap/app.php` (Lumen) you should replace Laravel's translation service provider 
if you have one, or if you don't, just add the following service provider aliases:

```php
'providers' => [
        \io3x1\FilamentTranslations\FilamentTranslationsProvider::class

'aliases' => [
        'translator' => \io3x1\FilamentTranslations\Extensions\DbTranslator::class
```


## Add Language Middleware

go to app/Http/Kernel.php and add new middleware to $middlewareGroups

```php
    'web' => [
        //...
        \io3x1\FilamentTranslations\Http\Middleware\LanguageMiddleware::class,
    ],
```

go to config/filament.php and add middleware to middleware auth array

```php
    'middleware' => [
        'auth' => [
            //...
            \io3x1\FilamentTranslations\Http\Middleware\LanguageMiddleware::class
        ],
        //...
    ];
```

and now clear cache

```bash
php artisan optimize:clear
```

To add the translations UI to your panel, add it to the `resources` method of your panel:
```php
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->resources([
                \io3x1\FilamentTranslations\Resources\TranslationResource::class,
            ])
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

-   [Fady Mondy](https://github.com/3x1io)
-   [Timo Railo](https://github.com/madviking)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
