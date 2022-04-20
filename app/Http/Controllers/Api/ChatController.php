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
        /** @var User $user  */
        $user = auth()->user();
        if ($request->title) {
            return response()->json(User::search($request->title)->where('id', "!=", $user->id)->get());
        }
        $chatsIds = ChatService::getChatIds();
        $chats = User::whereIn('id', $chatsIds)->where('id', "!=", $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json($chats);
    }

    public function getChat($id)
    {
        return User::find($id);
    }
}
