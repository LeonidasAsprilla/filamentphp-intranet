<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Mail\HolidayApproved;
use App\Mail\HolidayDeclined;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\HolidayResource;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model {
        $record->update($data);

        if ($record->type == 'approved'){
            $user = User::find($record->user_id);
            $dataToSend = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record['day'],
            );
            Mail::to($user)->send(new HolidayApproved($dataToSend));

            $recipient = $user;
            Notification::make()
                ->title('Solicitud de Vacaiones')
                ->body("Empleado: ". $user->name.". El día ".$data['day']." está aprobado.")
                ->sendToDatabase($recipient);

        }elseif ($record->type == 'decline'){
            $user = User::find($record->user_id);
            $dataToSend = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record['day'],
            );
            Mail::to($user)->send(new HolidayDeclined($dataToSend));

            $recipient = $user;
            Notification::make()
                ->title('Solicitud de Vacaiones')
                ->body("Empleado: ". $user->name.". El día ".$data['day']." está RECHAZADO.")
                ->sendToDatabase($recipient);
        }


    
        return $record;
    }
}
