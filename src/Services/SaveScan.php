<?php

namespace io3x1\FilamentTranslations\Services;

use Carbon\Carbon;
use io3x1\FilamentTranslations\Services\Scan;
use io3x1\FilamentTranslations\Models\Translation;
use Illuminate\Support\Facades\DB;
use io3x1\FilamentTranslations\Traits\TraitTranslator;

class SaveScan
{
    private $paths;

    // $this->db_translations[$text->namespace][$text->group][$text->locale][$text->key] = $text->text;
    public $db_translations = [];

    use TraitTranslator;

    public function __construct()
    {
        $this->paths = config('filament-translations.paths');
        $this->loadTranslationsFromDatabase();
    }

    public function save()
    {
        $scanner = app(Scan::class);

        collect($this->paths)->each(function ($path) use ($scanner) {
            $scanner->addScannedPath($path);
        });

        foreach($scanner->getAllViewFilesWithTranslations() as $key){
            $this->addTranslationItem($key, $group, $namespace, true);
        }
    }

}
