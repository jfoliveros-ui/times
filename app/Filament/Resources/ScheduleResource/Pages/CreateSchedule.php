<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\ScheduleValidator;
use Filament\Notifications\Notification;
use App\Models\Schedule;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $teacherId = $data['teacher_id'];
        $day = $data['working_day'];
        $mode = $data['mode'];
        $cetap = $data['cetap'];
        $asignature = $data['subject'];
        $semester = $data['semester'];
        $dates = $data['dates'];

        $conflictDates = [];

        foreach ($dates as $dateEntry) {
            $date = $dateEntry['date'];

            // 1. Validar conflictos por CETAP
            $cetapConflict = ScheduleValidator::validarCetap($cetap, $date, $semester, $day);

            if ($cetapConflict) {
                $conflictDates[] = [
                    'date' => $date,
                    'cetap' => $cetapConflict->cetap,
                    'mode' => $cetapConflict->mode,
                    'subject' => $cetapConflict->subject ?? 'Desconocido',
                    'type' => 'cetap',
                ];

                continue;
            }

            // 2. Validar conflictos por DOCENTE
            $docenteConflict = ScheduleValidator::validarDocente($teacherId, $date, $day, $mode, $cetap, $semester);

            if ($docenteConflict) {
                $conflictDates[] = [
                    'date' => $date,
                    'cetap' => $docenteConflict->cetap ?? 'Desconocido',
                    'subject' => $docenteConflict->subject ?? 'Desconocido',
                    'mode' => $docenteConflict->mode,
                    'semester'=> $semester,
                    'type' => 'docente',
                ];
            }
        }

        if (!empty($conflictDates)) {
            ScheduleValidator::notificarConflictos($conflictDates, $mode, $day);
            $this->halt();
        }

        foreach ($dates as $dateEntry) {
            $date = $dateEntry['date'];

            Schedule::create([
                'teacher_id' => $teacherId,
                'date' => $date,
                'working_day' => $day,
                'cetap' => $cetap,
                'mode' => $mode,
                'subject' => $asignature,
                'semester' => $semester,
            ]);

            Notification::make()
                ->title('Asignación de Horarios')
                ->body("Se ha asignado la asignatura {$asignature} con modalidad {$mode} el día {$date} en la jornada {$day}.")
                ->info()
                ->persistent()
                ->send();
        }

        $this->form->fill([]);
        $this->halt();
    }
}
