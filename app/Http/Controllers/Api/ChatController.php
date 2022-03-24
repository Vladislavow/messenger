<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function getChats(Request $request)
    {
        if ($request->title) {
            return response()->json(User::search($request->title)->get());
        }
        /** @var User $user  */
        $user = auth()->user();

        $unreadChats = Message::where('recipient', $user->id)
            ->where('read', false)
            ->select(DB::raw('`sender`, count(sender) as message_count'))
            ->groupBy('sender')
            ->get();
        $chatsIds = ChatService::getChatIds();
        $chats = User::whereIn('id', $chatsIds)->where('id', "!=", $user->id)->orderBy('created_at', 'desc')->get();
        $chats = $chats->map(function ($chat) use ($unreadChats) {
            $contactUnread = $unreadChats->where('sender', $chat->id)->first();
            $chat->unread = $contactUnread ? $contactUnread->message_count : 0;
            return $chat;
        });
        return response()->json($chats);
    }

    public function getChat($id)
    {
        return User::find($id);
    }
}
