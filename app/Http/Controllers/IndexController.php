<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

class IndexController extends Controller {
    public function index() {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('homepage');
        //return redirect()->route('login');
    }
}
