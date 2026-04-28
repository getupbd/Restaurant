<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
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

class TenantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tenant')
            ->path('admin')
            ->login()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->collapsedSidebarWidth('3.5rem')
            ->breadcrumbs(false)
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->font('Outfit')
            ->brandName('Antigravity Restaurant')
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Slate,
            ])
            ->discoverResources(in: app_path('Filament/Tenant/Resources'), for: 'App\\Filament\\Tenant\\Resources')
            ->discoverPages(in: app_path('Filament/Tenant/Pages'), for: 'App\\Filament\\Tenant\\Pages')
            ->pages([
                \App\Filament\Tenant\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tenant/Widgets'), for: 'App\\Filament\\Tenant\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->navigationItems([
                NavigationItem::make('Pending Orders')
                    ->url(fn() => route('filament.tenant.resources.orders.pending'))
                    ->icon('heroicon-o-clock')
                    ->group('Order Management')
                    ->sort(4),
                NavigationItem::make('Completed Orders')
                    ->url(fn() => route('filament.tenant.resources.orders.complete'))
                    ->icon('heroicon-o-check-badge')
                    ->group('Order Management')
                    ->sort(5),
                NavigationItem::make('Cancelled Orders')
                    ->url(fn() => route('filament.tenant.resources.orders.cancelled'))
                    ->icon('heroicon-o-x-circle')
                    ->group('Order Management')
                    ->sort(6),
            ])
            ->middleware([
                \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
                \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                \App\Http\Middleware\CheckTenantSubscription::class,
                \App\Http\Middleware\ApplyTenantSettings::class,
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
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::SIDEBAR_NAV_START,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@include("filament.tenant.sidebar-search")')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                fn (): string => '<div id="topbar-page-info" class="flex items-center gap-2 px-4 flex-1">
                    <div id="topbar-title" class="font-semibold text-gray-800 dark:text-white text-sm"></div>
                    <div id="topbar-actions-portal" class="flex items-center gap-2 ml-auto mr-2"></div>
                </div>'
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): string => '<script>
                    function movePageInfoToTopbar() {
                        var titleEl = document.getElementById("topbar-title");
                        var actionsPortal = document.getElementById("topbar-actions-portal");
                        if (!titleEl) return;

                        // Correct Filament v3 selectors
                        var h1      = document.querySelector(".fi-header-heading");
                        var actions = document.querySelector(".fi-header .fi-ac");
                        var header  = document.querySelector(".fi-header");

                        // 1. Move actions to topbar FIRST (before hiding header)
                        if (actionsPortal && actions) {
                            actionsPortal.innerHTML = "";
                            actionsPortal.appendChild(actions);
                        }

                        // 2. Set title text
                        if (h1) titleEl.textContent = h1.textContent.trim();

                        // 3. Now hide the page header
                        if (header) header.style.display = "none";
                    }
                    document.addEventListener("DOMContentLoaded", movePageInfoToTopbar);
                    document.addEventListener("livewire:navigated", function() { setTimeout(movePageInfoToTopbar, 60); });
                    document.addEventListener("livewire:navigate",   function() { setTimeout(movePageInfoToTopbar, 110); });
                </script>'
            );
    }
}
