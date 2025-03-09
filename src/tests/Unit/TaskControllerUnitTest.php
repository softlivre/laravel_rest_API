<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\TaskController;
use App\Models\Building;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the index method returns tasks without filters.
     *
     * @return void
     */
    public function testIndexReturnsTasksWithoutFilters(): void
    {
        // Create a building and a user.
        $building = Building::factory()->create(['id' => 1]);
        $user = User::factory()->create(['id' => 1]);

        // Create two tasks for the building.
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user->id,
            'assigned_to' => 1,
            'status'      => 'Open',
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user->id,
            'assigned_to' => $user->id,
            'status'      => 'In Progress',
        ]);

        // Create a GET request without filters.
        $request = Request::create("/api/buildings/{$building->id}/tasks", 'GET');

        // Instantiate the controller and call the index method.
        $controller = new TaskController();
        $response = $controller->index($request, $building->id);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('data', $data);
        $this->assertCount(2, $data['data']);
    }

    /**
     * Test that the index method returns tasks filtered by assigned_to.
     *
     * @return void
     */
    public function testIndexReturnsFilteredTasksByAssignedTo(): void
    {
        // Create a building.
        $building = Building::factory()->create();

        // Create two users.
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create tasks with different assigned_to values.
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user1->id,
            'assigned_to' => $user1->id,
            'status'      => 'Open',
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user1->id,
            'assigned_to' => $user2->id,
            'status'      => 'In Progress',
        ]);

        // Create a GET request with the assigned_to filter for $user1.
        $request = Request::create("/api/buildings/{$building->id}/tasks", 'GET', [
            'assigned_to' => $user1->id,
        ]);

        $controller = new TaskController();
        $response = $controller->index($request, $building->id);

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertCount(1, $data['data']);
        $this->assertEquals($user1->id, $data['data'][0]['assigned_to']);
    }

    /**
     * Test that the index method returns tasks filtered by status.
     *
     * @return void
     */
    public function testIndexReturnsFilteredTasksByStatus(): void
    {
        // Create a building and a user.
        $building = Building::factory()->create();
        $user = User::factory()->create();

        // Create tasks with different statuses.
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user->id,
            'status'      => 'Open',
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user->id,
            'status'      => 'Completed',
        ]);
        Task::factory()->create([
            'building_id' => $building->id,
            'created_by'  => $user->id,
            'status'      => 'Open',
        ]);

        // Create a GET request with the status filter "Open".
        $request = Request::create("/api/buildings/{$building->id}/tasks", 'GET', [
            'status' => 'Open',
        ]);

        $controller = new TaskController();
        $response = $controller->index($request, $building->id);

        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertCount(2, $data['data']);
        foreach ($data['data'] as $task) {
            $this->assertEquals('Open', $task['status']);
        }
    }

    /**
     * Test that the index method returns a validation error when given an invalid building ID.
     *
     * @return void
     */
    public function testIndexFailsValidationWithInvalidBuildingId(): void
    {
        // Use a non-existent building ID.
        $invalidBuildingId = 999;
        $request = Request::create("/api/buildings/{$invalidBuildingId}/tasks", 'GET');

        $controller = new TaskController();
        $response = $controller->index($request, $invalidBuildingId);

        $this->assertEquals(422, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Validation failed', $data['message']);
    }

    /**
     * Test that the store method creates a task successfully with valid data.
     *
     * @return void
     */
    public function testStoreCreatesTaskSuccessfully(): void
    {
        // Create a building and a user.
        $building = Building::factory()->create();
        $user = User::factory()->create();

        // Provide all required data, including created_by.
        $data = [
            'title'       => 'Test Task',
            'description' => 'Test description',
            'building_id' => $building->id,
            'assigned_to' => $user->id,
            'created_by'  => $user->id,
        ];

        $request = Request::create('/api/tasks', 'POST', $data);

        $controller = new TaskController();
        $response = $controller->store($request);

        $this->assertEquals(201, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertEquals('Test Task', $responseData['data']['title']);
    }

    /**
     * Test that the store method returns validation errors when provided invalid data.
     *
     * @return void
     */
    public function testStoreFailsValidationWithInvalidData(): void
    {
        // Provide invalid data: missing title and building_id.
        $data = [
            'title'       => '',
            'description' => 'Test description',
            'building_id' => null,
        ];

        $request = Request::create('/api/tasks', 'POST', $data);

        $controller = new TaskController();
        $response = $controller->store($request);

        $this->assertEquals(422, $response->getStatusCode());
        $responseData = $response->getData(true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertFalse($responseData['success']);
        $this->assertArrayHasKey('errors', $responseData);
    }
}
