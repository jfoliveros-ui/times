<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\Pages;

use App\Filament\Teacher\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        $docente = auth()->user()?->name ?? 'Docente';

        return "Asignaciones Docente: {$docente}";
    }
}
