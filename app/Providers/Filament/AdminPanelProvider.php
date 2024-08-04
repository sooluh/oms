<?php

namespace App\Providers\Filament;

use App\Filament\Pages;
use App\Filament\Resources;
use App\Models\Page;
use App\Settings\AppSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Pages\Login::class)
            ->passwordReset()
            ->colors(['primary' => Color::hex('#2CAE76')])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->authMiddleware([Authenticate::class])
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(false)
            ->collapsibleNavigationGroups(false)
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Profil')
                    ->icon('heroicon-o-user-circle')
                    ->url(static fn (): string => Pages\Profile::getUrl()),
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
            ->bootUsing(function (Panel $panel) {
                $panel->brandName(app(AppSettings::class)->name);
            })
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $pages = Page::query()
                    ->where('is_active', true)
                    ->orderBy('created_at', 'asc')
                    ->get();
                $items = $pages
                    ->map(fn (Page $page) => NavigationItem::make($page->title)
                        ->icon('heroicon-o-folder')
                        ->url(function () use ($page) {
                            return Resources\RecordResource::getUrl('index', [
                                'slug' => $page->slug,
                            ]);
                        })
                        ->isActiveWhen(function () use ($page) {
                            return request()->query('slug') === $page->slug &&
                                request()->routeIs(Resources\RecordResource::getRouteBaseName().'.*');
                        }))
                    ->toArray();

                return $builder->groups([
                    NavigationGroup::make('Ikhtisar')
                        ->items([
                            ...Pages\Dashboard::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Halaman')
                        ->items($items),
                    NavigationGroup::make('Kelola')
                        ->items([
                            ...Resources\PageResource::getNavigationItems(),
                            ...Pages\Settings::getNavigationItems(),
                        ]),
                ]);
            });
    }
}
