<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FallbackController extends Controller
{
    /**
     * Handle requests to undefined routes.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'This route does not exist. Check documentation at http://localhost:85/docs?api-docs.json'
        ], 404);
    }
}
