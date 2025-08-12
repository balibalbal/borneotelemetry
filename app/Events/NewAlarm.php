<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAlarm implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $information;
    public $customer_id;

    /**
     * Create a new event instance.
     */
    public function __construct($information, $customer_id)
    {
        $this->information = $information;
        $this->customer_id = $customer_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    
     public function broadcastOn()
     {
         return new PrivateChannel('alarm-channel.' . $this->customer_id);
     }
 
     public function broadcastAs()
     {
         return 'new-alarm';
     }
}
