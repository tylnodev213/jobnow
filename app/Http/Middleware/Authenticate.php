<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @param string[] ...$guards
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $area = getArea();

        if (!getGuard()->check()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.not_permission'),
                    'data' => [],
                ], 401);
            }

            return redirect()->route($area . '.login');
        }

        return $next($request);
    }
}
