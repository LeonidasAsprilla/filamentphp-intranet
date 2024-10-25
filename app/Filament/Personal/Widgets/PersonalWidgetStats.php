<?php

namespace App\Filament\Personal\Widgets;

use App\Models\User;
use App\Models\Holiday;
use App\Models\Timesheet;
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
            Stat::make('Horas Trabajadas', $this->getTotalWork(Auth::user())),
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
        $tiempoFormato = gmdate("H:i:s", $sumSeconds);
        // dd($tiempoFormato);
        return $tiempoFormato;
    }
}
