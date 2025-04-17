<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginEmailRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

class SessionEmailController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginEmailRequest $request): JsonResponse
    {
        $response = $request->verify();
        dd($request);

        return response()->json($response, 201);
    }
}
