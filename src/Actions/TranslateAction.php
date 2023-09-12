<?php

namespace io3x1\FilamentTranslations\Actions;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use io3x1\FilamentTranslations\Traits\TraitTranslator;

class TranslateAction extends Action
{
    use CanCustomizeProcess;
    use TraitTranslator;


    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'translate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-translations::translation.translate_single'));

        $this->modalHeading(fn (): string => __('filament-actions::edit.single.modal.heading', ['label' => $this->getRecordTitle()]));
        $this->modalSubmitActionLabel(__('filament-actions::edit.single.modal.actions.save.label'));
        $this->successNotificationTitle(__('filament-actions::edit.single.notifications.saved.title'));
        $this->icon('heroicon-o-language');

/*        $this->fillForm(function (Model $record, Table $table): array {
            if ($translatableContentDriver = $table->makeTranslatableContentDriver()) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });*/

        $this->action(function (): void {
            $this->process(function (array $data, Model $record, Table $table) {
                $this->translateIndividual($record);
            });

            $this->success();
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;
        return $this;
    }
}
