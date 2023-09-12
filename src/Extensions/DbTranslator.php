<?php

namespace io3x1\FilamentTranslations\Extensions;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\Translator;
use io3x1\FilamentTranslations\Models\Translation;
use io3x1\FilamentTranslations\Traits\TraitTranslator;
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

    use TraitTranslator;

    public $db_translations;

    public function __construct(Loader $loader, $locale)
    {
        $this->loader = $loader;
        $this->setLocale($locale);
        $this->loadTranslationsFromDatabase();
    }

    /*
     * This implements main get() method of Translator class for getting an individual translation string
     * 1. Do we have a cached DB version of the translation in the desired locale?
     *      -> if yes, return it
     * 2. Do we have a Laravel file translation in the desired locale?
     *      -> if yes, return it & add to database
     * 3. Do we have a cached DB version of the translation in English?
     *      -> if yes, translate it, add to database and return it
     * 4. Do we have a Laravel file translation in English?
     *      -> if yes, translate it, add all versions to db, add to database and return it
     *
     *
     * */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        if(!$locale){$locale = App::currentLocale() ?: 'en';}

        // 1.
        if ($translation = $this->keyExists($key, $locale)) {
            return $translation;
        }

        // 2.
        if($translation = parent::get($key, $replace, $locale, false)){
            return $this->addIfAllowed($key, $locale, false,$translation);
        }

        // 3.
        if ($translation = $this->keyExists($key, 'en')) {
            return $this->addIfAllowed($key, $locale, false,$translation);
        }

        // 4.
        if($translation = parent::get($key, $replace, 'en', false)){
            return $this->addIfAllowed($key, $locale, false,$translation);
        }

        return $key;
    }

    protected function addIfAllowed(string $key, string $locale = 'en', bool $do_translation=false, string $existing_string=''): string {
        if (config('filament-translations.auto_create')) {
            return $this->addTranslationItem($key, $locale, false, $existing_string);
        } elseif($existing_string){
            return $existing_string;
        }

        return $original_key;
    }


}
