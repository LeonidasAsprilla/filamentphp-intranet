<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class PersonalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('personal')
            ->path('personal')
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
            ])
            ->login()
            ->default()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Purple,
                'success' => Color::Amber,
                'warning' => Color::Orange,
            ])
            ->profile()
            ->discoverResources(in: app_path('Filament/Personal/Resources'), for: 'App\\Filament\\Personal\\Resources')
            ->discoverPages(in: app_path('Filament/Personal/Pages'), for: 'App\\Filament\\Personal\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Personal/Widgets'), for: 'App\\Filament\\Personal\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Suscribete!')
                ->url('https://www.youtube.com/@leonidasasprilla5425', shouldOpenInNewTab: true)
                ->icon('heroicon-o-heart')
                ->sort(3),
            ])
            ->topNavigation()
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn (): bool => auth()->user()->hasRole(Utils::getSuperAdminName())),
                // ...
            ]);
    }
}
