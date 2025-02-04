<?php

namespace App\Http\Middleware;

use Closure;
use illuminate\Support\Facades\Auth;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try{
            $user = Auth::payload();
        }catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            
            return response()->json(['token_expire'], 500);

        }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            
            return response()->json(['token_invalid' => $e->getMessage()], 500);
        }catch(\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e){
            
            return response()->json(['token_blacklist' => $e->getMessage()], 500);
        }catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
            
            return response()->json(['token_exception' => $e->getMessage()], 500);
        }

        return $next($request);
    }
}
