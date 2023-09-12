<?php

namespace io3x1\FilamentTranslations\Resources;

use App\Models\Translations;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;
use io3x1\FilamentTranslations\Actions\TranslateAction;
use io3x1\FilamentTranslations\Resources\TranslationResource\Pages\ManageTranslation;

class TranslationResource extends Resource
{
    protected static ?string $model = Translations::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('group')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('key')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('text')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('metadata'),
                Forms\Components\TextInput::make('namespace')
                    ->required()
                    ->maxLength(255)
                    ->default('*'),
            ]);
    }

    public static function table(Table $table): Table
    {

        Tables\Filters\SelectFilter::make('group')
            ->options([
                Translations::all()->pluck('group')->unique()->toArray()
            ]);

            /*->getFormField()
                ->label('Group')
                ->options('search')
                ->scope(fn (Builder $query, $searchTerm) => $query->where('key', 'like', "%{$searchTerm}%")
                    ->orWhere('group', 'like', "%{$searchTerm}%")
                    ->orWhere('namespace', 'like', "%{$searchTerm}%"));*/

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()->limit(35),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->searchable(),
                Tables\Columns\TextColumn::make('text')
                    ->searchable()->limit(35),
                Tables\Columns\TextColumn::make('namespace')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->translateLabel('Locale')
                    ->options(config('filament-translations.locales')),
                Tables\Filters\SelectFilter::make('group')
                    ->options(Translations::all()->pluck('group','group')->unique()->toArray()),
                Filter::make('is_empty')
                    ->translateLabel('Translation empty')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('text','=', ''))
                    ])
            ->actions([
                TranslateAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageTranslation::route('/'),
        ];
    }

    public function scan(): void
    {
        $scan = new SaveScan();
        $scan->save();
        $this->notify('success', 'Translation Has Been Loaded');
    }

}
