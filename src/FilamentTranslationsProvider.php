<?php

namespace io3x1\FilamentTranslations;

use Filament\Navigation\UserMenuItem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Filament\PluginServiceProvider;
use io3x1\FilamentTranslations\Extensions\DbTranslator;
use io3x1\FilamentTranslations\Resources\TranslationResource;
use Filament\Navigation\NavigationItem;
use Filament\Facades\Filament;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class FilamentTranslationsProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package->name('filament-translations');
    }

    protected array $resources = [
        TranslationResource::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->app->extend('translator', function ($translator, $app) {
            $loader = $translator->getLoader();  // Get the loader from the original translator
            $locale = App::getLocale();
            return new DbTranslator($loader, $locale);
        });

        //Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-translations');

        //Publish Lang
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament-translations'),
        ], 'filament-translations');

        //Publish Config
        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'filament-translations-config');

        //Register Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        //Check Show Switcher
        if (config('filament-translations.show-switcher')) {
            Filament::serving(function () {
                if(auth()->user()){
                    app()->setLocale(auth()->user()->lang);
                }
                if(config('filament-translations.languages-switcher-menu.position') === 'navigation'){
                    Filament::registerNavigationItems([
                        NavigationItem::make()
                            ->group(config('filament-translations.languages-switcher-menu.group'))
                            ->icon(config('filament-translations.languages-switcher-menu.icon'))
                            ->label(trans('filament-translations::translation.menu'))
                            ->sort(config('filament-translations.languages-switcher-menu.sort'))
                            ->url((string)url(config('filament-translations.languages-switcher-menu.url'))),
                    ]);
                }
                else if(config('filament-translations.languages-switcher-menu.position') === 'user'){
                    Filament::registerUserMenuItems($this->getLanguageMenuItemsForUser());
                }

                Filament::registerNavigationGroups([
                    config('filament-translations.languages-switcher-menu.group')
                ]);
            });
        }
    }

    private function getLanguageMenuItemsForUser(){
        $locales = config('filament-translations.locales');
        if(count($locales) < 2){ return []; }

        foreach ($locales as $locale => $locale_name) {
            $items[] = UserMenuItem::make()
                ->icon('heroicon-o-globe-europe-africa')
                ->label(trans($locale_name, [], $locale))
                ->sort(config('filament-translations.languages-switcher-menu.sort'))
                ->url((string)url(config('filament-translations.languages-switcher-menu.url')).'/'.$locale);
        }

        return $items;


    }
}
