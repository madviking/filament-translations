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


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use io3x1\FilamentTranslations\Models\Translation;

trait TraitTranslator
{



    protected function loadTranslationsFromDatabase()
    {

        if (config('filament-translations.cache')) {
            if ($cache = Cache::get('db-translations')) {
                $this->db_translations = $cache;
                return;
            }
        }

        $translations = Translation::where('deleted_at', null)->get();

        foreach ($translations as $text) {
            if (!$text->text or $text->text == '[]') {
                continue;
            }
            $this->db_translations[$text->namespace][$text->group][$text->locale][$text->key] = $text->text;
        }

        if (config('filament-translations.cache')) {
            Cache::put('db-translations', $this->db_translations, 60 * 24);
        }
    }


    private function splitKey(string $key): array
    {
        $namespace = '';
        $group = '*';

        if(strlen($key) > 200){
            dd($key);
        }

        if (stristr($key, '::')) {
            $namespace = explode('::', $key)[0];
            $key = explode('::', $key)[1];
        }

        if (stristr($key, '.')) {
            $group = explode('.', $key)[0];
        }

        return ['group' => $group, 'namespace' => $namespace, 'key' => $key];
    }

    /*
     * Adds translation and language versions. Careful with the do_translation parameter as it
     * can cause a loop if used incorrectly for doing translation.
     *
     * @var string $key - translation key, format scope::group.key
     * @var string $return_locale_string - return translation in this locale
     * @var bool $do_translation - whether to attempt translating with Laravel standard way
     * */
    protected function addTranslationItem(string $key, string $return_locale_string = 'en', bool $do_translation=false): string
    {
        if(!$key){ return ''; }

        extract($this->splitKey($key));

        if (isset($this->db_translations[$namespace][$group][$return_locale_string][$key])) {
            return $this->db_translations[$namespace][$group][$return_locale_string][$key];
        }

        $locales = config('filament-translations.add_all_locales') ? array_keys(config('filament-translations.locales')) : ['en'];

        if ($namespace and $namespace != '*') {
            $translation_key = $namespace . '::' . $key;
        } else {
            $translation_key = $key;
        }

        $original_english = $do_translation ? trans($translation_key, [], 'en') : $translation_key;
        $return = $original_english;

        foreach ($locales as $locale) {
            $original_localized = $do_translation ? trans($translation_key, [], $locale) : $translation_key;

            $exists = Translation::where('key', $key)
                ->where('group', $group)
                ->where('namespace', $namespace)
                ->where('locale', $locale)
                ->first();

            if($exists){ continue; }

            $translation = new Translation();
            $translation->key = $key;
            $translation->group = $group;
            $translation->namespace = $namespace;
            $translation->locale = $locale;

            /*
             * Apologies for hard to understand logic here.
             * */

            if($locale != 'en' and config('filament-translations.google_key') and config('filament-translations.google_translate') AND !$do_translation) {
                $translation->text = $this->googleTranslate($original_english, $locale);
            }elseif(!$do_translation){
                $translation->text = $key;
            }elseif ($original_localized != $key and $locale != 'en' and $original_localized != $namespace . '::' . $key) {
                $translation->text = $original_localized;
            } elseif ($locale != 'en' and config('filament-translations.google_key') and config('filament-translations.google_translate')) {
                $translation->text = $this->googleTranslate($original_english, $locale);
            } else {
                $translation->text = $original_english;
            }

            if ($return_locale_string == $locale) {
                $return = $translation->text;
            }

            $translation->save();
        }

        return $return;
    }

    /*
     * @var string $source_id - $db key for translation item to be Google translated from English
     * */
    public function translateIndividual(Model $model): bool {

        // get English version
        $exists = Translation::where('key', $model->key)
            ->where('group', $model->group)
            ->where('namespace', $model->namespace)
            ->where('locale', 'en')
            ->first();

        if(!$exists){
            return false;
        }

        try {
            $model->text = $this->googleTranslate($exists->text, $model->locale);
            $model->save();
        } catch (\Exception $e) {
            session()->flash('notification', [
                'message' => __(trans('There was an error in translating, check Google key')),
                'status' => "success",
            ]);
            return false;
        }

        session()->flash('notification', [
            'message' => __(trans('String translated')),
            'status' => "success",
        ]);

        return true;
    }

    public function googleTranslate(string $string, $to, $from = 'en'): string
    {
        $cache = Cache::get('google-translation-cache');

        if (isset($cache[$from][$to][$string])) {
            return $cache[$from][$to][$string];
        }

        $apikey = config('filament-translations.google_key');
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apikey . '&q=' . rawurlencode($string) . '&source=' . $from . '&target=' . $to;
        $data = file_get_contents($url);
        $data = json_decode($data, true);

        if (isset($data['data']['translations'][0]['translatedText'])) {
            $cache[$from][$to][$string] = $data['data']['translations'][0]['translatedText'];
            Cache::put('google-translation-cache', $cache);
            return $data['data']['translations'][0]['translatedText'];
        }

        return $string;
    }


}
