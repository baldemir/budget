<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Mail\PasswordChanged;
use App\Currency;
use Auth;
use Image;
use Storage;
use Hash;
use Mail;

class SettingsController extends Controller {
    public function getIndex() {
        return redirect()->route('settings.profile');
    }

    public function postIndex(Request $request) {
        $request->validate([
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif',
            'password' => 'nullable|confirmed',
            'language' => 'nullable|in:' . implode(',', array_keys(config('app.locales'))),
            'theme' => 'nullable|in:light,dark',
            'weekly_report' => 'nullable|in:true,false'
        ]);

        $user = Auth::user();

        // Profile
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $fileName = $file->hashName();

            $image = Image::make($file)
                ->fit(500);

            Storage::put('public/avatars/' . $fileName, (string) $image->encode());

            $user->avatar = $fileName;
            var_dump($fileName);die;
        }

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        // Account
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        if ($password = $request->input('password')) {
            $user->password = Hash::make($password);
        }

        // Preferences
        if ($request->has('language')) {
            $user->language = $request->input('language');
        }

        if ($request->has('theme')) {
            $user->theme = $request->input('theme');
        }

        if ($request->has('weekly_report')) {
            $user->weekly_report = $request->input('weekly_report') == 'true' ? true : false;
        }

        $user->save();

        // Notify upon changing of password
        if ($request->has('password')) {
            Mail::to($user->email)->queue(new PasswordChanged($user->updated_at));
        }

        return back();
    }

    public function getProfile() {
        return view('settings.profile');
    }

    public function getAccount() {
        return view('settings.account');
    }

    public function getSpaces() {
        return view('settings.spaces.index', [
            'spaces' => Auth::user()->spaces
        ]);
    }

    public function getPreferences() {
        $languages = [];

        foreach (config('app.locales') as $key => $value) {
            $flag = $key;

            if ($key == 'en') {
                $flag = 'us';
            }

            $languages[] = ['key' => $key, 'label' => '<i class="twf twf-sm twf-' . $flag . '"></i> ' . $value];
        }

        return view('settings.preferences', compact('languages'));
    }
}
