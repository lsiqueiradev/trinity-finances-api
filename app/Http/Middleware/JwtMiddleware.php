<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $public = ['api/register', 'api/sessions/password', 'api/sessions/email'];

        try {
            if (! in_array($request->path(), $public)) {
                JWTAuth::parseToken()->authenticate();
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token Expirado'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token inválido'], 401);
        } catch (Exception $e) {
            return response()->json(['message' => 'Token não encontrado'], 401);
        }

        return $next($request);
    }
}
