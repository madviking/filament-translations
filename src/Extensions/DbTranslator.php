<?php

namespace io3x1\FilamentTranslations\Extensions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\Translator;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/*
 * This package uses a different database structure compared to spatie/laravel-translation-loader package.
 *
 * Additional features (controlled from config file):
 *  - automatic addition of missing translation strings
 *  - automatic translation using Google Translate
 *
 * @author: timo at east.fi
 * */


class DbTranslator extends Translator
{

    public function get($key, array $replace = [], $locale = null, $fallback = true) {
        // Your custom logic here
        $locale = app()->getLocale();
        
        dd([
            'key' => $key,
            'replace' => $replace,
            'locale' => $locale,
            'fallback' => $fallback,
        ]);

        // For example, you can log every translation request
        \Log::info("Fetching translation for key: {$key}");

        // Call the parent method if you still want the default behavior after your custom logic
        return parent::get($key, $replace, $locale, $fallback);
    }

}
