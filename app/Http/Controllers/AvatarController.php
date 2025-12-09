<?php
namespace App\Http\Controllers;

use App\Http\Requests\User\AvatarPhotoStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvatarController extends Controller
{

    /**
     * Update the user data in storage.
     *
     * @param  UserUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(AvatarPhotoStoreRequest $request): JsonResponse
    {
        $request->user()->updateProfilePhoto($request->photo);
        return response()->json(['url' => $request->user()->profile_photo_url]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->deleteProfilePhoto();
        return response()->json([], status: 204);

    }
}
