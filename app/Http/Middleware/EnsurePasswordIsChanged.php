<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public const DEFAULT_PASSWORD = 'ohmyschool';

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        if (config('app.is_demo')) {
            return $next($request);
        }

        $profileUrl = route('filament.studio.pages.profile');

        if ($request->url() === $profileUrl) {
            return $next($request);
        }

        if (Hash::check(self::DEFAULT_PASSWORD, $user->password)) {
            return redirect()->to($profileUrl.'?tab='.urlencode('password::data::tab'));
        }

        return $next($request);
    }
}
