<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\ScheduleValidator;
use Filament\Notifications\Notification;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ScheduleMail;
use App\Models\Teacher;


class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $teacherId = $data['teacher_id'];
        $day = $data['working_day'];
        $mode = $data['mode'];
        $cetap = $data['cetap'];
        $subject = $data['subject'];
        $semester = $data['semester'];
        $dates = $data['dates'];

        $scheduleData = [];

        foreach ($dates as $dateEntry) {
            $baseDate = Carbon::parse($dateEntry['date']);

            switch ($day) {
                case 'Fin de Semana':
                    if ($baseDate->dayOfWeek === 5) { // viernes
                        $scheduleData[] = ['date' => $baseDate->copy()->toDateString(), 'working_day' => 'Noche'];
                        $scheduleData[] = ['date' => $baseDate->copy()->addDay()->toDateString(), 'working_day' => 'Mañana'];
                        $scheduleData[] = ['date' => $baseDate->copy()->addDay()->toDateString(), 'working_day' => 'Tarde'];
                        $scheduleData[] = ['date' => $baseDate->copy()->addDays(2)->toDateString(), 'working_day' => 'Mañana'];
                    }
                    break;

                case 'Viernes - Sábado':
                    if ($baseDate->dayOfWeek === 5) {
                        $scheduleData[] = ['date' => $baseDate->copy()->toDateString(), 'working_day' => 'Noche'];
                        $scheduleData[] = ['date' => $baseDate->copy()->addDay()->toDateString(), 'working_day' => 'Mañana'];
                    }
                    break;

                case 'Sábado - Domingo':
                    if ($baseDate->dayOfWeek === 6) {
                        $scheduleData[] = ['date' => $baseDate->copy()->toDateString(), 'working_day' => 'Tarde'];
                        $scheduleData[] = ['date' => $baseDate->copy()->addDay()->toDateString(), 'working_day' => 'Mañana'];
                    }
                    break;

                default:
                    $scheduleData[] = ['date' => $baseDate->toDateString(), 'working_day' => $day];
            }
        }

        // Validar los bloques generados
        $conflictDates = ScheduleValidator::validarConflictosExtendido(
            $teacherId,
            $cetap,
            $semester,
            $mode,
            $scheduleData
        );

        if (!empty($conflictDates)) {
            ScheduleValidator::notificarConflictos($conflictDates, $mode, $day);
            $this->halt();
        }

        // Crear los horarios
        foreach ($scheduleData as $item) {
            Schedule::create([
                'teacher_id' => $teacherId,
                'date' => $item['date'],
                'working_day' => $item['working_day'],
                'cetap' => $cetap,
                'mode' => $mode,
                'subject' => $subject,
                'semester' => $semester,
            ]);



            Notification::make()
                ->title('Asignación de Horarios')
                ->body("Asignado: {$subject}, {$mode}, {$item['date']} - {$item['working_day']}")
                ->info()
                ->persistent()
                ->send();
        }

        // Obtener al docente
        $teacher = Teacher::find($teacherId);

        // Compilar todas las fechas
        $fechasFormateadas = implode(', ', array_column($scheduleData, 'date'));

        $dataToSend = [
            'cetap' => $cetap,
            'subject' => $subject,
            'working_day' => $day,
            'dates' => $fechasFormateadas,
            'mode' => $mode,
        ];

        // Enviar el correo
        if ($teacher && $teacher->email) {

            Mail::to($teacher->email)->send(new ScheduleMail($dataToSend));
            Notification::make()
                ->title('Correo enviado')
                ->body("Se ha enviado un correo de asignación a {$teacher->full_name}")
                ->success()
                ->send();
        }

        $this->form->fill([]);
        $this->halt();
    }
}
