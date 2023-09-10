<?php

namespace io3x1\FilamentTranslations\Resources\TranslationsResource\Pages;

use App\Filament\Resources\TranslationsResource;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ManageRecords;

class ManageTranslations extends ManageRecords
{
    protected static string $resource = TranslationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('scan')->action('scan')->label(trans('translation.scan')),
            Action::make('settings')
                ->label('Settings')
                ->icon('heroicon-o-cog')
                ->form([
                    Select::make('language')
                        ->label('Language')
                        ->default(auth()->user()->lang)
                        ->options(config('filament-translations.locals'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $user = User::find(auth()->user()->id);

                    $user->lang = $data['language'];
                    $user->save();

                    session()->flash('notification', [
                        'message' => __(trans('translation.notification') . $user->lang),
                        'status' => "success",
                    ]);

                    redirect()->to('admin/translations');
                }),
        ];
    }

}
