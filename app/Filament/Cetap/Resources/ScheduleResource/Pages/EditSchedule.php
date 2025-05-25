<?php

namespace App\Filament\Cetap\Resources\ScheduleResource\Pages;

use App\Filament\Cetap\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    public bool $allowRender = true;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    public function mount($record): void
    {
        parent::mount($record);

        if (!in_array($this->record->commission, ['Aceptada'])) {
            Notification::make()
                ->title('Acceso Denegado')
                ->body('Solo puede modificar registros con comisión "Aceptada".')
                ->danger()
                ->send();

            $this->allowRender = false;

            $this->redirect(ScheduleResource::getUrl('index'));
        }
    }

    protected function shouldRender(): bool
    {
        return $this->allowRender;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Comisión actualizada correctamente')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
    }
}
