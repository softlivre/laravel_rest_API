<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FallbackControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an undefined route returns the proper fallback JSON response.
     *
     * @return void
     */
    public function testFallbackRouteReturnsJsonResponse()
    {
        // Send a GET request to a route that does not exist.
        $response = $this->getJson('/api/undefined-route');

        // Assert that the response has a 404 status code and the expected JSON message.
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'This route does not exist. Check documentation at http://localhost:85/docs?api-docs.json'
            ]);
    }
}
