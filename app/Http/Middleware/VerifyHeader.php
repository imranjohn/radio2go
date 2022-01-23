<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyHeader
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
        if($request->header('Accept') !== 'application/json'){
            return response()->json([
                "message" => "Accept in header is missing it should be application/json to proceed"
            ]);
        }
        return $next($request);
    }
}
