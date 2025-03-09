<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Models\Building;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a comment is created successfully with valid data.
     *
     * @return void
     */
    public function test_store_comment_with_valid_data()
    {
        // Create a building, a user, and then a task associated with that building.
        $building = Building::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'building_id' => $building->id,
        ]);

        // Data to be sent with the POST request
        $data = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => 'This is a test comment.',
        ];

        // Send a POST request to the comments endpoint
        $response = $this->postJson('/api/comments', $data);

        // Assert that the response status is 201 (Created) and the JSON structure is correct
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'task_id',
                    'user_id',
                    'comment',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Assert that the comment exists in the database
        $this->assertDatabaseHas('comments', [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => 'This is a test comment.',
        ]);
    }

    /**
     * Test that the store method returns validation errors with invalid data.
     *
     * @return void
     */
    public function test_store_comment_with_invalid_data()
    {
        // Sending an empty payload should trigger validation errors
        $data = [];

        // Send a POST request with empty data
        $response = $this->postJson('/api/comments', $data);

        // Assert that the response status is 422 (Unprocessable Entity) and the JSON structure includes errors
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }
}
