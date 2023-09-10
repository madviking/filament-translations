<?php

namespace io3x1\FilamentTranslations\Models;

use Illuminate\Support\Facades\Cache;
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


class Translation extends LanguageLine
{
    use HasFactory;
    use SoftDeletes;

    public $translatable = ['text'];

    /** @var array */
    public $guarded = ['id'];

    /** @var array */
    protected $casts = ['text' => 'array'];

    protected $table = "language_lines";

    protected $fillable = [
        "group",
        "key",
        "text",
        "locale"
    ];

    public function __construct()
    {
        return parent::__construct();
    }

    public static function getTranslatableLocales(): array
    {
        return config('filament-translations.locals');
    }

    public function getTranslation(string $locale, string $group = null): string
    {

        //dd($locale);
        // todo: add missing translation

        // todo: google translate
        if ($group === '*' && !isset($this->text[$locale])) {
            $fallback = config('app.fallback_locale');
            return $this->text[$fallback] ?? $this->key;
        }

        return $this->text[$locale] ?? '';
    }

    public function setTranslation(string $locale, string $value): self
    {
        $this->text = array_merge($this->text ?? [], [$locale => $value]);
        return $this;
    }

    protected function getTranslatedLocales(): array
    {
        return is_array($this->text) ? array_keys($this->text) : [];
    }
}
