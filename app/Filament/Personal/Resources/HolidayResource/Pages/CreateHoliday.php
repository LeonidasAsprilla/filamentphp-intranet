<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Mail\HolidayPending;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Personal\Resources\HolidayResource;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['type'] = 'pending';

        $dataToSend = array(
            'day' => $data['day'],
            'name' => User::find($data['user_id'])->name,
            'email' => User::find($data['user_id'])->email,
        );

        $userAdmin = User::find(1);
        Mail::to($userAdmin)->send(new HolidayPending($dataToSend));

        // Notification::make()
        //     ->title('Solicitud de Vacaciones')
        //     ->body("El día ".$data['day']." está pendiente de aprobar.")
        //     ->warning()
        //     ->send();

        $recipient = Auth::user();
        Notification::make()
            ->title('Solicitud de Vacaiones')
            ->body("El día ".$data['day']." está pendiente de aprobar.")
            ->sendToDatabase($recipient);
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
