<?php

namespace App\Http\Middleware;

use Closure;

class EnkelHulpverlener
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
      if( auth()->user()->type != "Hulpverlener"){
        return redirect('/');
      }
        return $next($request);
    }
}
