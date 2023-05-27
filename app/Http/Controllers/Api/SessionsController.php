<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(auth()->user()->tokens);
    }

    public function current(Request $request): JsonResponse
    {
        return response()->json(auth()->user()->currentAccessToken()->id);
    }

    public function destroy(Request $request, $tokenId): JsonResponse
    {
        auth()->user()->tokens()->where('id', $tokenId)->first()->delete();

        return response()->json('Session closed');
    }
}
