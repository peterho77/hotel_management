<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Breadcrumbs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName(); // Lấy tên route, ví dụ: 'about', 'room.index'

        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
        ];

        if ($routeName === 'about') {
            $breadcrumbs[] = ['label' => 'About us', 'url' => route('about')];
        }

        if (str_starts_with($routeName, 'room')) {
            $breadcrumbs[] = ['label' => 'Rooms', 'url' => route('room')];
        }

        // Có thể mở rộng tuỳ route khác (contact, blog, booking...)

        Inertia::share('breadcrumb', $breadcrumbs);

        return $next($request);
    }
}
