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

        if ($routeName === 'review') {
            $breadcrumbs[] = ['label' => 'Reviews', 'url' => route('review')];
        }

        if (str_starts_with($routeName, 'rooms')) {
            $breadcrumbs[] = ['label' => 'Rooms', 'url' => route('rooms.index')];

            if ($routeName === 'rooms.detail') {
                // Lấy room_id từ route
                $roomId = $request->route('id');
    
                // Lấy room_name từ DB
                $room = \App\Models\RoomType::find($roomId);  // tùy model bạn đặt tên
    
                $roomName = $room?->name ?? 'Room Detail';
    
                // Thêm vào breadcrumb
                $breadcrumbs[] = [
                    'label' => $roomName,
                    'url' => route('rooms.detail',  ['id' => $roomId]),
                ];
            }
        }

        // Có thể mở rộng tuỳ route khác (contact, blog, booking...)

        Inertia::share('breadcrumb', $breadcrumbs);

        return $next($request);
    }
}
