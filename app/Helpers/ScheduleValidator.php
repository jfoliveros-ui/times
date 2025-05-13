<?php

namespace App\Helpers;

use App\Models\Schedule;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ScheduleValidator
{
    // Validación para CETAP
    public static function validarCetap($cetap, $date, $semester, $day)
    {
        return Schedule::where('cetap', $cetap)
            ->where('date', $date)
            ->where('semester', $semester)
            ->where('working_day', $day)
            ->first();
    }

    // Validación para DOCENTE
    public static function validarDocente($teacherId, $date, $day, $mode, $cetap, $semester)
    {
        $existingSchedules = Schedule::where('teacher_id', $teacherId)
            ->where('date', $date)
            ->get();

        foreach ($existingSchedules as $schedule) {
            if (self::hayConflicto($mode, $day, $cetap, $schedule, $semester)) {
                return $schedule;
            }
        }

        return null;
    }

    private static function hayConflicto($mode, $day, $cetap, $schedule, $semester): bool
    {

        switch ($mode) {
            case 'Presencial':
                return (
                    ($schedule->mode == 'Presencial' && $schedule->working_day == $day) ||
                    ($schedule->mode == 'Virtual' && $schedule->working_day == $day) ||
                    ($schedule->mode == 'Hibrida' && $schedule->working_day == $day)
                );
            case 'Virtual':
                return (
                    ($schedule->mode == 'Presencial' && $schedule->working_day == $day) ||
                    ($schedule->mode == 'Virtual' && $schedule->working_day == $day && $schedule->cetap == $cetap) ||
                    ($schedule->mode == 'Hibrida' && $schedule->working_day == $day && $schedule->cetap == $cetap)
                );
            case 'Hibrida':
                return (
                    ($schedule->mode == 'Presencial' && $schedule->working_day == $day) ||
                    ($schedule->mode == 'Hibrida' && $schedule->semester == $semester) ||
                    ($schedule->mode == 'Virtual' && $schedule->working_day == $day && $schedule->cetap == $cetap)
                );
        }
        return false;
    }

    // Notificación de Conflictos
    public static function notificarConflictos(array $conflictDates, string $mode, string $day): void
    {

        $mensaje = "🚨 Se encontraron conflictos:\n\n";

        foreach ($conflictDates as $conflict) {
            $fechaFormateada = Carbon::parse($conflict['date'])->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

            if ($conflict['type'] === 'cetap') {
                $mensaje .= "🏫 CETAP: {$conflict['cetap']} - Asignatura: {$conflict['subject']} el {$fechaFormateada}\n";
            } elseif ($conflict['type'] === 'docente') {
                $mensaje .= "👨‍🏫 Docente ocupado en CETAP: {$conflict['cetap']} - Asignatura: {$conflict['subject']} - Con modalidad: {$conflict['mode']} el {$fechaFormateada}\n";
            }
        }

        $mensaje .= "\nJornada: {$day}";

        Notification::make()
            ->title('Conflicto de Horarios')
            ->body($mensaje)
            ->danger()
            ->persistent()
            ->send();
    }
}
