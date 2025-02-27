<?php

namespace io3x1\FilamentTranslations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use io3x1\FilamentTranslations\Traits\TraitTranslator;
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


class Translation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TraitTranslator;

    public $translatable = ['text'];
    /** @var array */
    public $guarded = ['id'];
    /** @var array */
    protected $casts = ['text' => 'string'];

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

    public function save(array $options = [])
    {
        self::flushCache();
        return parent::save($options); // TODO: Change the autogenerated stub
    }

    public static function flushCache()
    {
        Cache::delete('db-translations');
    }

    public static function getTranslatableLocales(): array
    {
        return config('filament-translations.locals');
    }

    /*
     * This loads translation group
     * */
    public function getTranslation(string $locale, string $group = null): string
    {

        $locale = $locale ?? App::currentLocale();

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
