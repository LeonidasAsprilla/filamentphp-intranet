<?php

namespace App\Filament\Personal\Widgets;

use App\Models\User;
use App\Models\Holiday;
use App\Models\Timesheet;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Vacaciones Pendientes', $this->getPendingHolidays(Auth::user())),
            Stat::make('Vacaciones Aprobadas', $this->getApprovedHolidays(Auth::user())),
            Stat::make('Horas Trabajadas (hoy)', $this->getTodayWork(Auth::user())),
            Stat::make('Horas en Descanso (hoy)', $this->getTodayPause(Auth::user())),
        ];
    }

    protected function getPendingHolidays(User $user){
        $totalPendingHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'pending')->get()->count();
        return $totalPendingHolidays;
    }

    protected function getApprovedHolidays(User $user){
        $totalAprovedHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'approved')->get()->count();
        return $totalAprovedHolidays;
    }

    protected function getTotalWork(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->get();

        $sumSeconds = 0;

        foreach ($timesheets as $timesheet) {
            $dayIn = Carbon::parse($timesheet->day_in);
            $dayOut = Carbon::parse($timesheet->day_out);
            $totalDuration = $dayOut->diffInSeconds($dayIn);            
            $sumSeconds = $sumSeconds + $totalDuration;
            // echo "Duración total: " . $totalDuration . "\n";
        }
        $sumSeconds = $sumSeconds * -1;
        $horas = floor($sumSeconds / 3600);
        $minutos = floor(($sumSeconds - ($horas * 3600)) / 60);
        $segundos = $sumSeconds - ($horas * 3600) - ($minutos * 60);
        // $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        // dd($tiempoFormato);
        return $horas . 'Hr. ' . $minutos . "min. " . floor($segundos) . "s.";
    }

    protected function getTodayWork(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->whereDate('created_at', Carbon::today())->get();

        $sumSeconds = 0;

        foreach ($timesheets as $timesheet) {
            $dayIn = Carbon::parse($timesheet->day_in);
            $dayOut = Carbon::parse($timesheet->day_out);
            $totalDuration = $dayOut->diffInSeconds($dayIn);            
            $sumSeconds = $sumSeconds + $totalDuration;
            // echo "Duración total: " . $totalDuration . "\n";
        }
        $sumSeconds = $sumSeconds * -1;
        $horas = floor($sumSeconds / 3600);
        $minutos = floor(($sumSeconds - ($horas * 3600)) / 60);
        $segundos = $sumSeconds - ($horas * 3600) - ($minutos * 60);
        // $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        // dd($tiempoFormato);
        return $horas . 'Hr. ' . $minutos . "min. " . floor($segundos) . "s.";
    }

    protected function getTotalPause(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type','pause')->get();
        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {
            # code...
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);
            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds = $sumSeconds + $totalDuration;
        }
        $sumSeconds = $sumSeconds * -1;
        $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        return $tiempoFormato;
    }

    protected function getTodayPause(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type','pause')->whereDate('created_at', Carbon::now())->get();
        $sumSeconds = 0;
        foreach ($timesheets as $timesheet) {
            # code...
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);
            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds = $sumSeconds + $totalDuration;
        }
        $sumSeconds = $sumSeconds * -1;
        $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        return $tiempoFormato;
    }
}
