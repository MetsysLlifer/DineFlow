<?php

namespace App\Events;

use App\Models\ActivityLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $log;

    public function __construct(ActivityLog $log)
    {
        $this->log = $log->toArray();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('activity');
    }

    public function broadcastAs(): string
    {
        return 'activity.logged';
    }
}
