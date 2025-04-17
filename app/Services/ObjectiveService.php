<?php
namespace App\Services;

use App\Models\Objective;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObjectiveService
{

    public function validateOwnership(string $objectiveId, Request $request): JsonResponse | Objective
    {
        try {
            $objective = Objective::findOrFail($objectiveId);

            if ($objective->user_id !== $request->user()->id) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => __('You do not have permission to access this resource.'),
                    ], 403)
                );
            }
            return $objective;
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('You do not have permission to access this resource.'),
                ], 403)
            );
        }
    }

    public function create(Request $request): Objective | bool
    {
        $objective = Objective::create(array_merge($request->all(), [
            'date'    => Carbon::parse($request->date)->setTimezone('UTC'),
            'user_id' => $request->user()->id,
        ]));

        if (! $objective) {
            return response()->json([
                'message' => __('An error occurred. Please try again.'),
            ], 403);
        }

        return $objective;
    }

    public function delete(Request $request, string $objectiveId): JsonResource | bool
    {
        $isDeleted = $this->validateOwnership($objectiveId, $request)->delete();

        if (! $isDeleted) {
            return response()->json([
                'message' => __('An error occurred. Please try again.'),
            ], 403);
        }

        return $isDeleted;
    }

    public function getAll(Request $request)
    {
        $status = $request->input('status', 'actived');
        return Objective::where([
            'status' => $status,
        ])->with(['deposits'])->get();
    }
}
