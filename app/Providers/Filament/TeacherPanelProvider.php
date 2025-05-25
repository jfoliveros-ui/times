<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teacher')
            ->path('teacher')
                        ->login()
            ->colors([
                'primary' => '#051367',
            ])
            ->renderHook('panels::body.start', fn() => '
                <style>
                    .fi-logo {
                        height: 3.5rem !important;
                    }
                    .fi-sidebar{
                        background-color: #051367 !important; /*Cambio de color de la barra lateral */
                    }
                    .fi-sidebar-item-label{
                        color: white !important; /*Cambio de color de texto */
                    }
                    .fi-sidebar-group-label{
                        color: white !important; /*Cambio de color de texto de los grupos */
                    }
                    .fi-sidebar-item-icon{
                        color: white !important; /*Cambio de color de iconos */
                    }
                    .fi-sidebar-item-active .fi-sidebar-item-icon{
                        color: black !important; /*Cambio de color de iconos al hacer click */
                    }
                        .fi-sidebar-item-active .fi-sidebar-item-label{
                        color: black !important; /*Cambio de color de texto al hacer click */
                    }
                    .fi-sidebar-item :hover .fi-sidebar-item-label{
                        color: black !important; /*Cambio de color de texto al pasar el mouse */
                    }
                    .fi-sidebar-item :hover .fi-sidebar-item-icon{
                        color: black !important; /*Cambio de color de texto al pasar el mouse */
                    }
                    .fc-h-event .fc-event-main {
                        white-space: normal !important; /*salto de linea en el calendario */
                    }
                        .filament-fullcalendar {
                        --fc-small-font-size : 0.65em !important; /*salto de linea en el calendario */
                    }
                    .fi-layout {
                        background-color: #DFF6FF;
                    }
                    a.fi-breadcrumbs-item-label:nth-child(1){
                        color: #051367 !important;
                    }
                    a.fi-breadcrumbs-item-label:nth-child(3){
                        color: #051367 !important;
                    }
                    svg.fi-breadcrumbs-item-separator:nth-child(1) {
                        fill-color: black !important;
                    }
                </style>
            ')
            ->font('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap')
            ->collapsibleNavigationGroups(false)
            ->brandLogo(asset('img/linux.svg'))
            ->discoverResources(in: app_path('Filament/Teacher/Resources'), for: 'App\\Filament\\Teacher\\Resources')
            ->discoverPages(in: app_path('Filament/Teacher/Pages'), for: 'App\\Filament\\Teacher\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Teacher/Widgets'), for: 'App\\Filament\\Teacher\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
