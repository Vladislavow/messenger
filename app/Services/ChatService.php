<?php

namespace App\Services;

use App\Models\Message;

class ChatService
{
    public static function getChatIds(): array
    {
        /** @var User $user  */
        $user = auth()->user();
        $messagesWithUser = Message::where('sender', $user->id)
            ->orWhere('recipient', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $recived = array_column(
            $messagesWithUser,
            'recipient'
        );
        $sent = array_column(
            $messagesWithUser,
            'sender'
        );
        $array = array_unique(array_merge($recived, $sent));
        return array_diff($array, [$user->id]);
    }
}
