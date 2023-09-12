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
            $this->db_translations[$text->locale][$text->namespace][$text->group][$text->key] = $text->text;
        }

        if (config('filament-translations.cache')) {
            Cache::put('db-translations', $this->db_translations, 60 * 24);
        }
    }


    private function splitKey(string $new_key): array
    {
        $namespace = '';
        $group = '*';

        if (stristr($new_key, '::')) {
            $namespace = explode('::', $new_key)[0];
            $new_key = explode('::', $new_key)[1];
        }

        if (stristr($new_key, '.')) {
            $group = explode('.', $new_key)[0];
        }

        return ['group' => $group, 'namespace' => $namespace, 'new_key' => $new_key];
    }

    protected function keyExists(string $new_key,string $return_locale_string): string{
        extract($this->splitKey($new_key));

        if (isset($this->db_translations[$return_locale_string][$namespace][$group][$new_key])) {
            return $this->db_translations[$return_locale_string][$namespace][$group][$new_key];
        }

        return '';
    }

    /*
     * Adds translation and language versions. Careful with the do_translation parameter as it
     * can cause a loop if used incorrectly for doing translation.
     *
     * @var string $new_key - translation key, format scope::group.key
     * @var string $return_locale_string - return translation in this locale
     * @var bool $do_translation - whether to attempt translating with Laravel standard way
     * */
    protected function addTranslationItem(string $original_key, string $return_locale_string = 'en', bool $do_translation=false, string $existing_string=''): string
    {
        // make sure we are working with the latest data
        $this->loadTranslationsFromDatabase();

        // this holds the return value
        $return = $existing_string;

        if(!$original_key){ return ''; }

        extract($this->splitKey($original_key));

        if ($exists = $this->keyExists($original_key,$return_locale_string)) {
            return $exists;
        }

        // go through all locales when adding
        $locales = config('filament-translations.add_all_locales') ? array_keys(config('filament-translations.locales')) : ['en'];

        if ($namespace and $namespace != '*') {
            $translation_key = $namespace . '::' . $new_key;
        } else {
            $translation_key = $new_key;
        }

        if(!$existing_string){
            $existing_string = $do_translation ? trans($translation_key, [], 'en') : $translation_key;
        }

        foreach ($locales as $locale) {
            $string_to_add = $existing_string;

            $exists = Translation::where('key', $new_key)
                ->where('group', $group)
                ->where('namespace', $namespace)
                ->where('locale', $locale)
                ->first();

            if($exists){ continue; }

            // in this case we do translation, otherwise we are saving either existing string that has been sent to this function or the original key
            if(config('filament-translations.google_key') and config('filament-translations.google_translate')){
                if($locale != $return_locale_string){
                    $string_to_add = $this->googleTranslate($existing_string, $locale, $return_locale_string);
                }
            }

            $translation = new Translation();
            $translation->key = $new_key;
            $translation->group = $group;
            $translation->namespace = $namespace;
            $translation->locale = $locale;
            $translation->text = $string_to_add;

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
