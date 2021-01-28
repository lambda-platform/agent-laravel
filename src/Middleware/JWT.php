<?php

namespace Lambda\Agent\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWT
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            } else {
                if (request()->ajax()) {
                    return response()->json(['error' => 'unauthorized'], 401);
                }

                return redirect()->to('auth/login');
            }
        } catch (JWTException $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'unauthorized'], 401);
            }

            return redirect()->to('auth/login');
        }

         if (JWTAuth::parseToken()->authenticate()) {
             return $next($request);
         } else {
             return redirect()->to('auth/login');
         }

//        try {
//            JWTAuth::parseToken()->authenticate();
//        } catch (Exception $e) {
//            if ($e instanceof TokenInvalidException) {
//                $status = 401;
//                $message = 'This token is invalid. Please Login';
//                return response()->json(compact('status', 'message'), 401);
//            } else if ($e instanceof TokenExpiredException) {
//                try {
//                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
//                    JWTAuth::setToken($refreshed)->toUser();
//                    $request->headers->set('Authorization', 'Bearer ' . $refreshed);
//                } catch (JWTException $e) {
//                    return response()->json([
//                        'code' => 103,
//                        'message' => 'Token cannot be refreshed, please Login again'
//                    ]);
//                }
//            } else {
//                $message = 'Authorization Token not found';
//                return response()->json(compact('message'), 404);
//            }
//        }
        return $next($request);
    }
}
