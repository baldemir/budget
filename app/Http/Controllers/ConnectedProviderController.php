<?php

namespace App\Http\Controllers;

use App\Account;
use App\ConnectedProvider;
use Illuminate\Http\Request;

use App\Spending;
use App\Tag;

use Auth;

class ConnectedProviderController extends Controller {

    public function index(Request $request) {
        $filter = false;

        $connectedProviders = ConnectedProvider::all();
        return view('providers.index', compact('connectedProviders'));
    }

    public function edit(ConnectedProvider $provider) {
        $this->authorize('edit', $provider);

        $accounts = Account::where('provider_id', $provider->provider_id)->get();
        return view('providers.edit', compact('provider', 'accounts'));
    }


}
