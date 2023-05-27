<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function getChatIds($user): array
    {
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

    public function getChats($user, $title = null): Collection
    {
        return $title
            ? User::search($title)->where('id', "!=", $user->id)->get()
            : User::whereIn('id', $this->getChatIds($user))->where('id', "!=", $user->id)->orderBy('created_at', 'desc')->get();
    }
}
