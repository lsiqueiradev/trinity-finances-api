<?php
namespace App\Http\Controllers;

use App\Http\Requests\Objective\ObjectiveCreateUpdateRequest;
use App\Models\Objective;
use App\Services\ObjectiveService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectiveController extends Controller
{

    /**
     * Display a listing of all objectives.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $objectives = app(ObjectiveService::class)->getAll($request);

        return response()->json($objectives);
    }

    /**
     * Create a new objective.
     *
     * @param  ObjectiveCreateUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ObjectiveCreateUpdateRequest $request): JsonResponse
    {
        app(ObjectiveService::class)->create($request);

        return response()->json([
            'message' => __('Objective created successfully'),
        ], 201);

    }

    public function show(string $objectiveId)
    {
        //
    }

    public function update(ObjectiveCreateUpdateRequest $request, string $objectiveId)
    {
        try {
            app(ObjectiveService::class)->create($request);

            return response()->json([
                'message' => __('Objective created successfully'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('An error occurred while creating the objective. Please try again.'),
            ], 403);
        }

    }

    public function destroy(Request $request, string $objectiveId)
    {
        app(ObjectiveService::class)->delete($request, $objectiveId);

        return response()->json([
            'message' => __('Objective deleted successfully'),
        ], 201);

    }
}
