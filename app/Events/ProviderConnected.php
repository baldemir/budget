<?php

namespace App\Events;

use App\Account;
use App\Activity;
use App\ConnectedProvider;
use App\Provider;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class ProviderConnected{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(ConnectedProvider $connectedProvider) {
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::user()->id;
        }

        Activity::create([
            'space_id' => 1,
            'user_id' => $userId,
            'entity_id' => $connectedProvider->provider_id,
            'entity_type' => 'connected_provider',
            'action' => 'provider.connected'
        ]);
    }
}
