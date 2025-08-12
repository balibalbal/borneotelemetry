<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class NotificationsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // $totalNotifications = Notification::where('status', 'baru')->count(); // Menghitung total notifikasi
        // $notifications = Notification::where('status', 'baru')
        //                       ->orderBy('id', 'desc')
        //                       ->limit(10)
        //                       ->get();
        // View::share('totalNotifications', $totalNotifications);
        // View::share('notifications', $notifications);

        // return $next($request);
    }
}
