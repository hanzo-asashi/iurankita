<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\ConstructionFeesChart;
use App\Filament\Widgets\ConstructionStatsOverview;
use App\Filament\Widgets\MonthlyPaymentsChart;
use App\Filament\Widgets\MonthlyStatsOverview;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('IuranKita')
            ->brandLogo(fn () => asset('images/logo/49-light.png'))
            ->darkModeBrandLogo(fn () => asset('images/logo/49-dark.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(fn () => asset('images/logo/49-favicon.png'))
            ->login(Login::class)
            ->spa()
            ->databaseNotifications()
            ->databaseTransactions()
            ->profile()
            ->maxContentWidth(Width::Full)
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->recoverable(),
            )
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Teal,
                'secondary' => Color::Emerald,
                'gray' => Color::Gray,
                'blue' => Color::Blue,
                'green' => Color::Green,
                'red' => Color::Red,
                'yellow' => Color::Yellow,
                'rose' => Color::Rose,
                'cyan' => Color::Cyan,
                'purple' => Color::Purple,
                'indigo' => Color::Indigo,
                'violet' => Color::Violet,
                'sky' => Color::Sky,
                'slate' => Color::Slate,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                MonthlyStatsOverview::class,
                ConstructionStatsOverview::class,
                MonthlyPaymentsChart::class,
                ConstructionFeesChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL + K',
                Platform::Mac => '⌘ + K',
                default => null,
            });
    }
}
