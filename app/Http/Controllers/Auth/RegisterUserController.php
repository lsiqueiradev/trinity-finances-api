<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RegisterUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userCreated = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($userCreated
        ));

        $user = $userCreated
            ->only(['email', 'name', 'profile_photo_url']);
        $token = JWTAuth::fromUser($userCreated
        );

        return response()->json([
            'user'          => $user,
            'token'         => $token,
            'refresh_token' => $token,
        ], 201);
    }
}
