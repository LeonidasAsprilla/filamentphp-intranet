<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Holiday;
use App\Models\Timesheet;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type','pending')->count();
        $totalTimesheets = Timesheet::all()->count();
        return [
            //
            Stat::make('Empleados', $totalEmployees),
                // ->description('32k increase')
                // ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Vacaciones Pendientes', $totalHolidays),
            Stat::make('Hojas de Tiempo', $totalTimesheets),
            // Stat::make('Unique views', '192.1k')
            //     ->description('32k increase')
            //     ->descriptionIcon('heroicon-m-arrow-trending-up')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('success'),
        ];
    }
}
