<?php

namespace App\Http\Middleware;

use Closure;

class CheckBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (empty(session()->get('branch')))
        {
            return redirect('select-branch');
        }
        return $next($request);
    }
}
