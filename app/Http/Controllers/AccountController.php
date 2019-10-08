<?php

namespace App\Http\Controllers;

use App\Account;
use App\Http\Controllers\Integration\IntegrationController;
use Illuminate\Http\Request;

use Auth;
use App\Tag;

class AccountController extends Controller {
    private $validationRules = [
        'name' => 'required|max:255',
        'color' => 'required|max:6'
    ];

    public function index() {
        return view('accounts.index', [
            'accounts' => session('space')->accounts()->orderBy('created_at', 'DESC')->get()
        ]);
    }

    public function create() {
        return view('tags.create');
    }

    public function store(Request $request) {
        $request->validate($this->validationRules);

        Tag::create([
            'space_id' => session('space')->id,
            'name' => $request->input('name'),
            'color' => $request->input('color')
        ]);

        return redirect()->route('tags.index');
    }

    public function edit(Account $account) {
        $this->authorize('edit', $account);

        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account) {
        $this->authorize('update', $account);

        $request->validate($this->validationRules);

        $account->fill([
            'name' => $request->input('name'),
            'color' => $request->input('color'),
            'description' => $request->input('description')
        ])->save();

        return redirect()->route('accounts.index');
    }

    public function destroy(Account $account) {
        $this->authorize('delete', $account);

        if (!$account->spendings->count() && !$account->earnings->count()) {
            $account->delete();
        }

        return redirect()->back();
    }

    public function updateStatus(Request $request, Account $account){
        $this->authorize('update', $account);
        if($request->get('status') != null){
            $account->status = 1;
        }else{
            $account->status = 0;
        }
        $account->save();

        return redirect()->back();
    }
}
