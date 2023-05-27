<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Message;
use App\Models\User;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService)
    {
    }

    public function stream(Request $request, Attachment $attachment): BinaryFileResponse|JsonResponse
    {
        $message = $attachment->message;
        /** @var User $user */
        $user = auth()->user();
        if ($user->id != $message->sender && $user->id != $message->recepient) {
            return response()->json('Forbidden', 403);
        }

        $response = new BinaryFileResponse($attachment->path);
        BinaryFileResponse::trustXSendfileTypeHeader();

        return $response;
    }

    public function download(Request $request, Attachment $attachment): BinaryFileResponse|JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $message = Message::find($attachment->message_id);

        if ($user->id == $message->sender || $user->id == $message->recipient) {
            return response()->download($attachment->path);
        } else {
            return response()->json('Access denied', 503);
        }
    }

    public function destroy(Request $request, Attachment $attachment): JsonResponse
    {
        $this->attachmentService->destroy($attachment);

        return response()->json('Attachment deleted', 200);
    }
}
