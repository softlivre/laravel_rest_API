<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Retrieve a list of tasks for a given building, with optional filtering.
     *
     * This method validates that the provided building ID is valid and exists,
     * fetches the building, and then returns its tasks along with any associated
     * comments. Optional query parameters allow filtering tasks by the user the task
     * is assigned to (`assigned_to`) and the task status (`status`).
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @param  int                                 $buildingId The ID of the building.
     * @return \Illuminate\Http\JsonResponse       JSON response containing the tasks or error details if validation fails.
     * 
     *
     * 
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
    public function index(Request $request, $buildingId): \Illuminate\Http\JsonResponse
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

        // Start building the query for tasks with their comments
        $query = $building->tasks()->with('comments');

        // Optional filter for assigned_to
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->query('assigned_to'));
        }

        // Optional filter for status (e.g., Open, In Progress, Completed, Rejected)
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $tasks = $query->get();

        return response()->json([
            'data' => $tasks,
        ]);
    }

    /**
     * Create a new task.
     *
     * This method validates the incoming request using rules defined in rulesForStoreTask().
     * If validation passes, it sets the 'created_by' field to the authenticated user's ID (or a fallback value),
     * defaults the task status to "Open", and creates a new task record.
     * On success, it returns a JSON response with the created task data; otherwise, it returns validation errors.
     *
     * @param \Illuminate\Http\Request $request     The HTTP request instance containing task details.
     * @return \Illuminate\Http\JsonResponse        JSON response with task data on success or error messages on failure.
     * 
     * 
     * 
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
