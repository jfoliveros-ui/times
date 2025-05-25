<?php

namespace App\Filament\Cetap\Resources\ScheduleResource\Pages;

use App\Filament\Cetap\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
{
    return parent::getTableQuery()
        ->whereIn('commission', ['No Cumplida', 'Cumplida', 'Aceptada']);
}
}
