<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

/**
 *  @OA\GET(
 *      path="/api/buildings/{buildingId}/tasks",
 *      summary="List tasks for a building",
 *      description="Returns a list of tasks for a specified building along with their comments. If the building does not exist, returns a 404 response with an error message.",
 *      tags={"Tasks"},
 *      @OA\Parameter(
 *          name="buildingId",
 *          in="path",
 *          description="The ID of the building",
 *          required=true,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Tasks retrieved successfully",
 *          @OA\MediaType(
 *              mediaType="application/json"
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Building not found",
 *          @OA\MediaType(
 *              mediaType="application/json"
 *          )
 *      )
 *  )
 */
class TaskController extends Controller
{
    /**
     * List tasks for a specific building along with their comments.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($buildingId)
    {
        // Manually fetch the building by its id
        $building = Building::find($buildingId);

        if (!$building) {
            return response()->json([
                'message' => 'Building not found'
            ], 404);
        }

        // Eager load tasks with their comments
        $tasks = $building->tasks()->with('comments')->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }
}
