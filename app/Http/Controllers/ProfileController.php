<?php
namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->only(['email', 'name', 'profile_photo_url']);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->all());
        return response()->json($user->only(['email', 'name']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
