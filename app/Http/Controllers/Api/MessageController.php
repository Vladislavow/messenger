<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    const DEFAULT_MESSAGES_PAGINATION = 40;
    public function __construct(private readonly MessageService $messageService)
    {
    }

    public function index(Request $request, int $chatId): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $this->messageService->markAsRead($user, $chatId);

        return response()->json($this->messageService->getChatMessages($user, $chatId)
            ->orderBy('id', 'desc')
            ->paginate(self::DEFAULT_MESSAGES_PAGINATION));
    }

    public function markAsRead(Request $request, $chatId): Response
    {
        /** @var User $user */
        $user = auth()->user();

        $this->messageService->markAsRead($user, $chatId);

        return response()->noContent();
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $validatedFields = $request->validated();

        $newMessage = $this->messageService->store($validatedFields, $request->attachment);

        return response()->json($newMessage);
    }

    public function update(UpdateMessageRequest  $request, Message $message): JsonResponse
    {
        $validatedFields = $request->validated();

        $this->messageService->update($message, $validatedFields, $request->attachment);

        return response()->json($message);
    }

    public function destroy(Request $request, Message $message): JsonResponse
    {
        $this->messageService->destroy($message);

        return response()->json('deleted', 200);
    }
}
