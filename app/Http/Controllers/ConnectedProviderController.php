<?php

namespace App\Http\Controllers;

use App\Account;
use App\ConnectedProvider;
use App\Provider;
use Illuminate\Http\Request;

use App\Spending;
use App\Tag;

use Auth;

class ConnectedProviderController extends Controller {

    public function index(Request $request) {
        $filter = false;
        $user = Auth::user();
        $connectedProviders = ConnectedProvider::where('user_id', $user->id)->get();
        return view('providers.index', compact('connectedProviders'));
    }

    public function edit(ConnectedProvider $provider) {
        $this->authorize('edit', $provider);

        $accounts = Account::where('provider_id', $provider->provider_id)->get();
        return view('providers.edit', compact('provider', 'accounts'));
    }

    public function create() {
        $providers = Provider::all();
        $user = Auth::user();
        $connectedProviders = ConnectedProvider::where('user_id', $user->id)->get();



        return view('providers.create', compact('providers', 'connectedProviders'));
    }

}
