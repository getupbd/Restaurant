<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Colors\Color;

class ApplyTenantSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            $name = Setting::get('restaurant_name', 'Antigravity Restaurant');
            $logo = Setting::get('logo', null);
            $color = Setting::get('primary_color', '#4f46e5');

            $panel = Filament::getCurrentPanel();
            if ($panel) {
                $panel->brandName($name);
                if ($logo) {
                    $panel->brandLogo(asset('storage/' . $logo));
                }
            }

            FilamentColor::register([
                'primary' => Color::hex($color),
            ]);
        }

        return $next($request);
    }
}
