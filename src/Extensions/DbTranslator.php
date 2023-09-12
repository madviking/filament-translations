<?php

namespace io3x1\FilamentTranslations\Extensions;

use Illuminate\Contracts\Translation\Loader;
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
     * */
    public function get($key, array $replace = [], $locale = null, $fallback = true) {

        // try the default if it's missing
        if(!$locale){
            $locale = app()->getLocale();
        }

        // db overrides files
        if(isset($this->db_translations[$locale][$key])){
            return $this->db_translations[$locale][$key];
        }

        // we try the default method
        $translation = parent::get($key, $replace, $locale, $fallback);

        // default method might return a key
        if($translation == $key OR !$translation){
            $translation = $this->addTranslationItem($key, $locale);
        }

        return $translation;
    }


}
