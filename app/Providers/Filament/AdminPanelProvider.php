<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use CodeWithDennis\FilamentThemeInspector\FilamentThemeInspectorPlugin;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use ReflectionClass;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        // $panelHooks = new ReflectionClass(PanelsRenderHook::class);
        // Table Hooks
        // $tableHooks = new ReflectionClass(ViewTablesRenderHook::class);
        // // Widget Hooks
        // $widgetHooks = new ReflectionClass(Widgets\View\WidgetsRenderHook::class);

        // $panelHooks = $panelHooks->getConstants();
        // $tableHooks = $tableHooks->getConstants();
        // $widgetHooks = $widgetHooks->getConstants();

        // foreach ($panelHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }
        // foreach ($tableHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }
        // foreach ($widgetHooks as $hook) {
        //     $panel->renderHook($hook, function () use ($hook) {
        //         return Blade::render('<div style="border: solid red 1px; padding: 2px;">{{ $name }}</div>', [
        //             'name' => Str::of($hook)->remove('tables::'),
        //         ]);
        //     });
        // }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            // ->plugins([
            //     FilamentThemeInspectorPlugin::make()
            //         ->disabled(fn() => ! app()->hasDebugModeEnabled())
            //         ->toggle()
            // ])
            ->authMiddleware([
                Authenticate::class,
            ])
            //logo
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->favicon('/images/logo-trx.png')
            ->brandLogo('/images/logo-trx.png')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->globalSearch()
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, function () {
                return Blade::render('<div style="padding: 2px; font-size: 10px;">{{ $text }}</div>', [
                        'text' => 'Versión: 1.0'
                    ]);
            });
            // ->renderHook(PanelsRenderHook::TOPBAR_END, function () {
            //         return Blade::render('<div style="padding: 2px; font-size: 20px;">{{ $text }}</div>', [
            //             'text' => '52.000.000'
            //         ]);
            // });
    }
}