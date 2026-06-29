<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn () => '
                <style>
                    /* ══════════════════════════════════════
                       AUTH PAGE — split-screen layout
                    ══════════════════════════════════════ */
                    .custom-auth-wrapper {
                        display: flex;
                        width: 100%;
                        min-height: 100vh;
                        flex-direction: column;
                    }
                    .custom-auth-empty-panel {
                        position: relative;
                        display: flex;
                        flex-direction: column;
                        flex-grow: 1;
                        overflow: hidden;
                        background-color: var(--empty-panel-background-color);
                    }
                    .custom-auth-form-panel {
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        width: 100%;
                        min-height: 100vh;
                        padding: 2.5rem 2rem;
                        background-color: #ffffff;
                    }
                    .custom-auth-form-wrapper {
                        margin: 0 auto;
                        width: 100%;
                        max-width: 22rem;
                    }
                    @media (min-width: 1024px) {
                        .custom-auth-wrapper { flex-direction: row; }
                        .custom-auth-form-panel {
                            width: var(--form-panel-width, 42%);
                            flex-shrink: 0;
                            padding: 3rem 4rem;
                            min-height: unset;
                        }
                    }
                    .custom-auth-form-panel .fi-simple-header { text-align: left; margin-bottom: 2rem; }
                    .custom-auth-form-panel .fi-simple-header-heading { font-size: 1.75rem; font-weight: 700; color: #111827; line-height: 1.25; margin-top: 0; }
                    .custom-auth-form-panel .fi-simple-header-subheading { font-size: 0.875rem; color: #6b7280; margin-top: 0.375rem; }
                    .fi-auth-footer { text-align: center; font-size: 0.72rem; color: #9ca3af; margin-top: 1.75rem; }

                    /* ══════════════════════════════════════
                       SIDEBAR — #1C398E dark blue theme
                    ══════════════════════════════════════ */
                    .fi-sidebar,
                    .fi-body-has-topbar .fi-sidebar-header,
                    :not(.fi-body-has-topbar) .fi-sidebar-header {
                        background-color: #1C398E !important;
                    }

                    /* Nav item labels */
                    .fi-sidebar-item-label,
                    .fi-sidebar-group-label,
                    .fi-sidebar-database-notifications-btn-label {
                        color: rgba(255,255,255,0.80) !important;
                    }

                    /* Nav item icons */
                    .fi-sidebar-item-btn > .fi-icon,
                    .fi-sidebar-group-btn > .fi-icon,
                    .fi-sidebar-database-notifications-btn > .fi-icon {
                        color: rgba(255,255,255,0.55) !important;
                    }

                    /* Hover state */
                    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover,
                    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible {
                        background-color: rgba(255,255,255,0.08) !important;
                    }

                    /* Active item */
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                        background-color: rgba(255,255,255,0.14) !important;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon {
                        color: #ffffff !important;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label {
                        color: #ffffff !important;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-grouped-border-part {
                        background-color: rgba(255,255,255,0.6) !important;
                    }

                    /* Separator lines inside sidebar */
                    .fi-sidebar-item-grouped-border-part-not-first,
                    .fi-sidebar-item-grouped-border-part-not-last {
                        background-color: rgba(255,255,255,0.2) !important;
                    }

                    /* ══════════════════════════════════════
                       MAIN CONTENT AREA — pure white
                    ══════════════════════════════════════ */
                    .fi-body { background-color: #ffffff !important; }
                    .fi-main-ctn { background-color: #ffffff !important; }
                    .fi-main { background-color: #ffffff !important; }
                </style>
            '
        );

        // "Protected admin area" footer below the form
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn () => '<p class="fi-auth-footer">Protected admin area &middot; YourSaaS Platform</p>'
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('Saas-App')
            ->colors([
                'primary' => Color::hex('#2563eb'),
            ])
            ->plugins([
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('right')
                    ->formPanelWidth('42%')
                    ->emptyPanelView('filament.auth.brand-panel'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
