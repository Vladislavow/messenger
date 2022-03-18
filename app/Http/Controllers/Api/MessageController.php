<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;

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
        $messages = Message::where(function ($query) use ($id, $user) {
            $query->where('sender', $user->id);
            $query->where('recipient', $id);
        })->orWhere(function ($query) use ($id, $user) {
            $query->where('recipient', $user->id);
            $query->where('sender', $id);
        })->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($messages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(StoreMessageRequest $request)
    {
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
        /** @var User $user */
        $user = auth()->user();
        Message::where(function ($query) use ($request, $user) {
            $query->where('sender', $request->recipient);
            $query->where('recipient', $user->id);
        })->where('read', false)->update(['read' => true]);
        $message = Message::create($validatedFields);
        $message->read = false;
        broadcast(new NewMessage($message));
        return response()->json($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMessageRequest  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
