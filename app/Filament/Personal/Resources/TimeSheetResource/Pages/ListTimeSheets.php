<?php

namespace App\Filament\Personal\Resources\TimeSheetResource\Pages;

use Filament\Actions;
use App\Models\Timesheet;
use Filament\Actions\Action;
use PhpParser\Node\Expr\New_;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Personal\Resources\TimeSheetResource;
use Carbon\Carbon;

class ListTimeSheets extends ListRecords
{
    protected static string $resource = TimeSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inwork')
                ->label('Entrar a Trabajar')
                ->requiresConfirmation()
                // ->keyBindings(['command+s', 'ctrl+s'])
                ->color('success')
                ->action(function(){
                    $user = Auth::user();
                    $timesheet = New Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->day_out = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                }),
            Action::make('inpause')
                ->label('Comenzar Pausa')
                ->requiresConfirmation()
                ->color('info'),
            Actions\CreateAction::make(),
        ];
    }
}
