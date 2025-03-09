<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Models\Building;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the store method creates a comment successfully when provided valid data.
     *
     * @return void
     */
    public function testStoreValidData(): void
    {
        // Create a building, a user, and a task (with a valid building_id)
        $building = Building::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['building_id' => $building->id]);

        $data = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => 'This is a valid test comment.',
        ];

        // Create a fake POST request.
        $request = Request::create('/api/comments', 'POST', $data);

        // Instantiate the controller and call the store method.
        $controller = new CommentController();
        $response = $controller->store($request);

        // Assert that a JsonResponse is returned with status code 201.
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        // Assert that the response JSON has the expected structure.
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('id', $responseData['data']);
    }

    /**
     * Test that the store method returns validation errors when provided invalid data.
     *
     * @return void
     */
    public function testStoreInvalidData(): void
    {
        // Sending an empty payload should trigger validation errors.
        $data = [];

        // Create a fake POST request with empty data.
        $request = Request::create('/api/comments', 'POST', $data);

        // Instantiate the controller and call the store method.
        $controller = new CommentController();
        $response = $controller->store($request);

        // Assert that a JsonResponse is returned with status code 422.
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());

        // Assert that the response JSON contains error details.
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData);
    }
}
