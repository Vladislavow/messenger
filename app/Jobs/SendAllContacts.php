<?php

namespace App\Jobs;

use App\Services\ChatService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

class SendAllContacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ChatService $chatService;
    protected string $event;
    protected $param;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $event, $param)
    {
        $this->chatService = app()->make(ChatService::class);
        $this->event = $event;
        $this->param = $param;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = auth()->user();
        $idsArray = $this->chatService->getChatIds($user);
        foreach ($idsArray as $id) {
            broadcast(new $this->event($this->param, $id));
        }
    }
}
