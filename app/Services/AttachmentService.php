<?php

namespace App\Services;

use App\Events\MessageUpdated;
use App\Models\Attachment;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class AttachmentService {
    public function storeAttachment($files, $message): void
    {
        if ($files && count($files) > 0) {
            foreach ($files as $file) {
                $path = 'storage/' . $file->store('/attachment', 'public');
                $attachment = Attachment::create([
                    'message_id' => $message->id,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'extension' => pathinfo($path, PATHINFO_EXTENSION),
                ]);
            }
        }
    }

    public function destroy($attachment)
    {
        $message_id = $attachment->message->id;

        if (Storage::exists($attachment->path)) {
            Storage::delete($attachment->path);
        }

        $attachment->delete();

        broadcast(new MessageUpdated(Message::with('attachments')->where('id', $message_id)->first()));
    }
}
