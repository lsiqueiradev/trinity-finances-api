<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SessionPasswordController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginPasswordRequest $request): JsonResponse
    {
        $response = $request->authenticate();

        return response()->json($response, 201);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(): Response
    {
        Auth::logout();

        return response()->noContent();
    }
}
