<?php

namespace App\Policies;

use App\ConnectedProvider;
use App\Provider;
use App\User;
use App\Tag;

class ConnectedProviderPolicy {
    public function edit(User $user, ConnectedProvider $provider) {
        return $user->connectedProviders->contains($provider->id);
    }
    public function update(User $user, ConnectedProvider $provider) {
        return $user->connectedProviders->contains($provider->id);
    }

    public function delete(User $user, ConnectedProvider $provider) {
        return $user->connectedProviders->contains($provider->id);
    }
}
