<?php

namespace App\Domain\Agents\Events;

use App\Domain\Thread\ThreadRun;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RunFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ThreadRun $threadRun;
    public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ThreadRun $threadRun, $request)
    {
        $this->threadRun = $threadRun;
        $this->request = $request;
    }
}