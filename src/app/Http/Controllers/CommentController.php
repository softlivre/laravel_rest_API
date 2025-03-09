<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Create a new comment.
     *
     * This method validates the incoming request using rules defined in rulesForStoreComment().
     * If validation is successful, it creates a new comment record using the validated data.
     * On success, it returns a JSON response containing the created comment with a 201 status code;
     * otherwise, it returns a JSON response with validation error messages and a 422 status code.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing comment data.
     * @return \Illuminate\Http\JsonResponse JSON response with the created comment data or validation errors.
     * 
     * 
     * 
     *  @OA\POST(
     *      path="/api/comments",
     *      summary="Create a new comment for a task",
     *      description="Creates a new comment for a specified task. The endpoint requires task_id, user_id, and comment text as query parameters.",
     *      tags={"Comments"},
     *      @OA\Parameter(
     *          name="task_id",
     *          in="query",
     *          description="The ID of the task",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user_id",
     *          in="query",
     *          description="The ID of the user creating the comment",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="comment",
     *          in="query",
     *          description="The comment text",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Comment created successfully",
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $this->rulesForStoreComment());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        // Create the comment record
        $comment = Comment::create($validatedData);

        return response()->json(['data' => $comment], 201);
    }

    public function rulesForStoreComment(): array
    {
        return [
            'task_id' => 'required|integer|exists:tasks,id',
            'user_id' => 'required|integer|exists:users,id',
            'comment' => 'required|string',
        ];
    }
}
