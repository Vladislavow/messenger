<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chatService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user  */
        $user = auth()->user();

        return response()->json($this->chatService->getChats($user, $request->input('title')));
    }

    public function show($id): JsonResponse
    {
        return response()->json(User::find($id));
    }
}
