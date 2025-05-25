<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\Pages;

use App\Filament\Teacher\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

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

        if (!in_array($this->record->commission, ['Asignada'])) {
            Notification::make()
                ->title('Acceso Denegado')
                ->body('Solo puede modificar registros con comisión "Asignada".')
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
