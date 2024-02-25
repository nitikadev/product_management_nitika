<?php

namespace App\Http\Middleware;

use App\Services\Api\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            $token = $request->header('Authorization');

            if (!$token || !str_starts_with($token, 'Bearer ')) {
                return ApiResponse::unauthorized('Authentication failed');
            }

            $token = str_replace('Bearer ', '', $token);
            $token = JWTAuth::setToken($token)->getPayload();

            // You can access the authenticated user
        } catch (TokenExpiredException $e) {
            // Token has expired
            return ApiResponse::unauthorized('Token Expired');
        } catch (TokenInvalidException $e) {
            return ApiResponse::unauthorized('Invalid Token');
        } catch (JWTException $e) {
            // General exception
            return ApiResponse::unauthorized('Authentication failed');
        }
        return $next($request);
    }
}
