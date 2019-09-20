<?php

namespace App\Http\Controllers;

use App\Account;
use App\ConnectedProvider;
use App\Policies\ConnectedProviderPolicy;
use App\Provider;
use Illuminate\Http\Request;

use App\Spending;
use App\Tag;

use Auth;

class ConnectedProviderController extends Controller {

    public function index(Request $request) {
        $filter = false;

        $providers = Provider::all();
        return view('providers.index', compact('providers'));
    }

    public function edit(ConnectedProvider $provider) {
        $this->authorize('edit', $provider);

        $accounts = Account::where('provider_id', $provider->provider_id)->get();
        return view('providers.edit', compact('provider', 'accounts'));
    }


}
