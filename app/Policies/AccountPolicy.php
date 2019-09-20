<?php

namespace App\Policies;

use App\Account;
use App\User;
use App\Tag;

class AccountPolicy {
    public function edit(User $user, Account $account) {
        return $user->spaces->contains($account->space_id);
    }

    public function update(User $user, Account $account) {
        return $user->spaces->contains($account->space_id);
    }

    public function delete(User $user, Account $account) {
        return $user->spaces->contains($account->space_id);
    }
}
