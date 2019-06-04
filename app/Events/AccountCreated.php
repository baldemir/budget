<?php

namespace App\Events;

use App\Account;
use App\Activity;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Auth;

class AccountCreated{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(Account $account) {
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::user()->id;
        }

        Activity::create([
            'space_id' => $account->space_id,
            'user_id' => $userId,
            'entity_id' => $account->id,
            'entity_type' => 'account',
            'action' => 'account.created'
        ]);
    }
}
