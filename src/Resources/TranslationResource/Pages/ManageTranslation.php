<?php

namespace io3x1\FilamentTranslations\Resources\TranslationResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use io3x1\FilamentTranslations\Resources\TranslationResource;
use io3x1\FilamentTranslations\Services\SaveScan;

class ManageTranslation extends ManageRecords
{
    protected static string $resource = TranslationResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('flushCache')->action('flushCache')->label(trans('filament-translations::translation.flush_cache')),
            Action::make('scan')->action('scan')->label(trans('filament-translations::translation.scan')),
        ];
    }

    public function flushCache(){

        Artisan::call('cache:clear');
        Cache::delete('db-translations');

        session()->flash('notification', [
            'message' => __(trans('Translation caches flushed')),
            'status' => "success",
        ]);
    }

    public function scan(): void
    {
        $scan = new SaveScan();
        $scan->save();

        session()->flash('notification', [
            'message' => __(trans('Translation Has Been Loaded')),
            'status' => "success",
        ]);
    }


}
