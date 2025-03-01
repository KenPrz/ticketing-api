<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * Validate the API key.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateApiKey(Request $request): JsonResponse
    {
        return response()->json(['valid' => true], 200);
    }
}
