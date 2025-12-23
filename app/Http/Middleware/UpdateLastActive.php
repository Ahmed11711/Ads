<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UpdateLastActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            try {
                $cacheKey = 'user_last_active_' . $user->id;

                if (!Cache::has($cacheKey)) {
                    $user->update(['last_active_at' => now()]);
                    Cache::put($cacheKey, true, now()->addMinutes(5));
                }
            } catch (\Exception $e) {
                // ممكن تسجل الخطأ في log بس متأثرش على المستخدم
                // \Log::error("Failed to update last_active_at for user {$user->id}: " . $e->getMessage());
            }
        }

        return $next($request);
    }
}
