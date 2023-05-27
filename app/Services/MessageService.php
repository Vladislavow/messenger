<?php

namespace App\Services;

use App\Events\HasRead;
use App\Events\MessageDeleted;
use App\Events\MessageUpdated;
use App\Events\NewMessage;
use App\Models\Attachment;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;

class MessageService
{
    public function __construct(private readonly AttachmentService $attachmentService)
    {
    }

    public function markAsRead($user, $chatId): void
    {
        Message::where(function ($query) use ($chatId, $user) {
            $query->where('sender', $chatId);
            $query->where('recipient', $user->id);
        })->where('read', false)->update(['read' => true]);

        broadcast(new HasRead($chatId));
    }

    public function getChatMessages($user, $chatId): Builder
    {
        return Message::with('attachments')
            ->where(function ($query) use ($chatId, $user) {
                $query->where('sender', $user->id);
                $query->where('recipient', $chatId);
            })->orWhere(function ($query) use ($chatId, $user) {
                $query->where('recipient', $user->id);
                $query->where('sender', $chatId);
            });
    }

    public function store($fields, $attachment): Message|Builder
    {
        $fields['read'] = false;
        $message = Message::create($fields);

        $this->attachmentService->storeAttachment($attachment, $message);
        $messageWithAttachments = Message::with('attachments')->find($message->id);

        broadcast(new NewMessage($messageWithAttachments));

        return $messageWithAttachments;
    }

    public function update($message, $fields, $attachments): Message|Builder
    {
        $message->update($fields);
        $this->attachmentService->storeAttachment($attachments, $message);
        $updatedMessage = Message::with('attachments')->find($message->id);

        broadcast(new MessageUpdated($updatedMessage));

        return $updatedMessage;
    }

    public function destroy($message): void
    {
        $message->delete();

        broadcast(new MessageDeleted($message));
    }
}
