<?php

namespace App\Http\Controllers\Api;

use App\Events\HasRead;
use App\Events\MessageDeleted;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Attachment;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $id)
    {
        /** @var User $user */
        $user = auth()->user();
        Message::where(function ($query) use ($id, $user) {
            $query->where('sender', $id);
            $query->where('recipient', $user->id);
        })->where('read', false)->update(['read' => true]);
        $messages = Message::with('attachments')->where(function ($query) use ($id, $user) {
            $query->where('sender', $user->id);
            $query->where('recipient', $id);
        })->orWhere(function ($query) use ($id, $user) {
            $query->where('recipient', $user->id);
            $query->where('sender', $id);
        })->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($messages);
    }

    public function markAsRead($id)
    {
        /** @var User $user */
        $user = auth()->user();
        Message::where(function ($query) use ($id, $user) {
            $query->where('sender', $id);
            $query->where('recipient', $user->id);
        })->where('read', false)->update(['read' => true]);
        broadcast(new HasRead($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageRequest $request)
    {
        $validatedFields = $request->validated();
        $message = Message::create($validatedFields);
        $message->read = false;
        $this->storeAttachment($request->attachment, $message);
        $message = Message::with('attachments')->find($message->id);
        broadcast(new NewMessage($message));
        return response()->json($message);
    }

    public function storeAttachment($files, $message)
    {
        if ($files && count($files) > 0) {
            foreach ($files as $file) {
                $attachment = new Attachment();
                $attachment->message_id = $message->id;
                $attachment->original_name = $file->getClientOriginalName();
                $attachment->path = 'storage/' . $file->store('/attachment', 'public');
                $attachment->extension = pathinfo($attachment->path, PATHINFO_EXTENSION);
                $attachment->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        $message->delete();
        broadcast(new MessageDeleted($message));
        return response()->json('deleted', 200);
    }
}
