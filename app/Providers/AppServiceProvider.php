<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'azul'          => Color::hex('#42708C'),
            'marronOscuro'  => Color::hex('#40403F'),
            'naranja'       => Color::hex('#F26513'),
            'marronClaro'   => Color::hex('#8C4014'),
            'negro'         => Color::hex('#0D0D0D'),
            'disabilitado'  => Color::hex('#A9A9A9'),
        ]);

        //RenderHook
        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            function () {
                return view('footer');
            }
        );
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::TOPBAR_START,
        //     function () {
        //         return view('search');
        //     }
        // );
    }
}
