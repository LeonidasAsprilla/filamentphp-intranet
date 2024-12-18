<?php

namespace App\Filament\Personal\Resources\TimeSheetResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Timesheet;
use Filament\Actions\Action;
use PhpParser\Node\Expr\New_;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Filament\Personal\Resources\TimeSheetResource;
use App\Imports\MyTimesheetImport;

class ListTimeSheets extends ListRecords
{
    protected static string $resource = TimeSheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id','desc')->first();
        if($lastTimesheet == null){
            return [
                Action::make('inwork')
                    ->label('Entrar a Trabajar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function(){
                        $user = Auth::user();
                        $timesheet = New Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = 'work';
                        $timesheet->save();
                    }),
                Actions\CreateAction::make(),
            ];
        }
        return [
            Action::make('inwork')
                ->label('Entrar a Trabajar')
                ->keyBindings(['command+i', 'ctrl+i'])
                ->color('success')
                ->visible(!$lastTimesheet->day_out == null)
                ->disabled($lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function(){
                    $user = Auth::user();
                    $timesheet = New Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    // $timesheet->day_out = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();

                    Notification::make()
                        ->title('Has entrado a trabajar')
                        ->body('Has comenzado a trabajar a las:'.Carbon::now())
                        ->color('success')
                        ->success()
                        ->send();
                }),
            Action::make('stopWork')
                ->label('Parar de trabajar')
                ->keyBindings(['command+o', 'ctrl+o'])
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    Notification::make()
                        ->title('Has parado de trabajar')
                        ->success()
                        ->color('success')
                        ->send();
                }),
            Action::make('inpause')
                ->label('Comenzar Pausa')
                ->color('info')
                ->requiresConfirmation()
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->action(function () use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';
                    $timesheet->save();
                    Notification::make()
                        ->title('Comienzas tu pausa')
                        ->color('info')
                        ->info()
                        ->send();
                }),
            Action::make('stopPause')
                ->label('Parar Pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type=='pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                    Notification::make()
                        ->title('Comienzas de nuevo a trabajar')
                        ->color('info')
                        ->info()
                        ->send();
                }),
            ExcelImportAction::make()
                ->color("primary")
                ->use(MyTimesheetImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
