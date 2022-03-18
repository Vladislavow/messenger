<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getChats()
    {
        /** @var User $user  */
        $user = auth()->user();
        $messagesWithUser = Message::where('sender', $user->id)
            ->orWhere('recipient', $user->id)
            ->orderBy('created_at')
            ->get()
            ->toArray();
        $unreadChats = Message::where('recipient', $user->id)
            ->where('read', false)
            ->select(DB::raw('`sender`, count(sender) as message_count'))
            ->groupBy('sender')
            ->get();
        $recived = array_column(
            $messagesWithUser,
            'recipient'
        );
        $sent = array_column(
            $messagesWithUser,
            'sender'
        );
        $messages = array_unique(array_merge($recived, $sent));
        $chats = User::whereIn('id', $messages)->where('id', "!=", $user->id)->get();
        $chats = $chats->map(function ($chat) use ($unreadChats) {
            $contactUnread = $unreadChats->where('sender', $chat->id)->first();
            $chat->unread = $contactUnread ? $contactUnread->message_count : 0;
            return $chat;
        });
        return response()->json($chats);
    }
}
