<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     *  @OA\POST(
     *      path="/api/comments",
     *      summary="Create a new comment for a task",
     *      description="Creates a new comment for a specified task. The endpoint requires task_id, user_id, and comment text.",
     *      tags={"Comments"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"task_id", "user_id", "comment"},
     *              @OA\Property(property="task_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=2),
     *              @OA\Property(property="comment", type="string", example="This is a sample comment."),
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
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'comment' => 'required|string',
        ]);

        // Create the comment record
        $comment = Comment::create($validatedData);

        return response()->json(['data' => $comment], 201);
    }
}
