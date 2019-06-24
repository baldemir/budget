<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class VerifyController extends Controller {
    public function __invoke($token) {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->verification_token = null;
        $user->save();
        
        return redirect()
            ->route('login')
            ->with([
                'alert_type' => 'success',
                'alert_message' => Lang::get('general.email_verified_successfully')
            ]);;
    }
}
