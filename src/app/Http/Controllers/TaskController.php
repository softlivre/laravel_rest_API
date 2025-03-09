<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List tasks for a specific building along with their comments.
     *
     * @param  \App\Models\Building  $building
     * @return \Illuminate\Http\JsonResponse
     */

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
    public function index($buildingId)
    {
        // Validate that buildingId is an integer and exists in the buildings table
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['buildingId' => $buildingId],
            [
                'buildingId' => 'required|integer|exists:buildings,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Fetch the building from the database
        $building = Building::find((int) $buildingId);

        // Load tasks with their comments
        $tasks = $building->tasks()->with('comments')->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }

    /**
     *  @OA\POST(
     *      path="/api/tasks",
     *      summary="Create a new task",
     *      description="Creates a new task for a building. The task is created with a default status of 'Open'.",
     *      tags={"Tasks"},
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          description="Title of the task",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              example="Fix Leak in Apartment 5B"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          description="Description of the task",
     *          required=false,
     *          @OA\Schema(
     *              type="string",
     *              example="Water leak detected in the bathroom."
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="building_id",
     *          in="query",
     *          description="ID of the building",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="assigned_to",
     *          in="query",
     *          description="ID of the user assigned to the task",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              example=2
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Task created successfully",
     *          @OA\MediaType(
     *              mediaType="application/json"
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *  )
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $this->rulesForStoreTask());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // Set created_by from the authenticated user, or fallback if not using auth
        $validatedData['created_by'] = auth()->id() ?? $request->input('created_by', 1);

        // Set default status to "Open"
        $validatedData['status'] = 'Open';

        // Create the task record
        $task = Task::create($validatedData);

        return response()->json(['data' => $task], 201);
    }

    public function rulesForStoreTask(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'building_id' => 'required|integer|exists:buildings,id',
            'assigned_to' => 'nullable|integer|exists:users,id',
        ];
    }
}
