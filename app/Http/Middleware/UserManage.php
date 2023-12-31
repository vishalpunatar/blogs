<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserManage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //To check that user is authenticated and user status shoule be 1(1 = Enable/ 0 = Disable)
        if(auth()->user() && auth()->user()->status == 1){
            return $next($request);
        }
        return response()->json(['message'=>'Sorry You Are Temporary Disable by Admin'],403);
    }
}
