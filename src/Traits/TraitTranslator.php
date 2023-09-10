<?php

namespace io3x1\FilamentTranslations\Traits;


/*
 * This package uses a different database structure compared to spatie/laravel-translation-loader package.
 *
 * Additional features (controlled from config file):
 *  - automatic addition of missing translation strings
 *  - automatic translation using Google Translate
 *
 * @author: timo at east.fi
 * */


trait TraitTranslator
{
    private function translateWithGoogle(string $key, string $locale, array $replace = [], string $en_string = ''): string {
        try {

        } catch (\Exception $e) {
            // translation failed
        }

        return $this->addTranslation($key, $replace, $locale, $translation);
    }

    private function addTranslation(string $key, string $locale, array $replace = [], $translation = ''): string {

    }

}
