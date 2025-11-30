<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action; // created|updated|deleted
    public array $product;

    public function __construct(string $action, Product $product)
    {
        $this->action = $action;
        $this->product = $product->toArray();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('products');
    }

    public function broadcastAs(): string
    {
        return 'product.changed';
    }
}
