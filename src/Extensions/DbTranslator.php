<?php

namespace io3x1\FilamentTranslations\Extensions;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\Translator;
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

    public function __construct(Loader $loader, $locale)
    {
        $this->loader = $loader;
        $this->setLocale($locale);
    }

    /*
     * This implements main get() method of Translator class for getting an individual translation string
     * */
    public function get($key, array $replace = [], $locale = null, $fallback = true) {

        // try the default if it's missing
        if(!$locale){
            $locale = app()->getLocale();
        }

        // we try the default method
        $translation = parent::get($key, $replace, $locale, $fallback);

        // default method might return a key
        if($translation == $key OR !$translation){
            // let's try again in English
            if($locale != 'en'){
                $translation = parent::get($key, $replace, 'en', $fallback);
            }

            // translation is done only in the case of auto creation, when there is English original to translate from
            if(config('filament-translations.auto_create')
                && config('filament-translations.auto_translate')
                && $translation
                && $translation != $key
                && $locale != 'en'
            ){
                return $this->translateWithGoogle($key, $locale, $replace, $translation);
            } elseif(config('filament-translations.auto_create')) {
                return $this->addTranslation($key, $locale, $replace, $translation);
            }
        }

        return $translation;
/*        dd([
            'key' => $key,
            'replace' => $replace,
            'locale' => $locale,
            'fallback' => $fallback,
        ]);*/

    }


}
