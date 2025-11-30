<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $order;

    public function __construct(Order $order)
    {
        $this->order = $order->toArray();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('orders');
    }

    public function broadcastAs(): string
    {
        return 'order.status';
    }
}
