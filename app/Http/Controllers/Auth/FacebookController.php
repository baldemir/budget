<?php
/**
 * Created by PhpStorm.
 * User: ulakbim
 * Date: 31.07.2019
 * Time: 11:25
 */

namespace App\Http\Controllers\Auth;


use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use Exception;
use Auth;


class FacebookController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            $create['name'] = $user->getName();
            $create['email'] = $user->getEmail();
            $create['facebook_id'] = $user->getId();
            $create['verification_token'] = str_random(100);


            $userModel = new User;

            $existingUser = User::where('email', $user->getEmail())->first();
            if($existingUser == null){
                $createdUser = $userModel->addNew($create);
            }else{
                $existingUser->facebook_id = $user->getId();
                $existingUser->save();
                $createdUser = $existingUser;
            }
            Auth::loginUsingId($createdUser->id);


            return redirect()->route('home');


        } catch (Exception $e) {

            var_dump($e->getMessage());die;
            return redirect('auth/facebook');


        }
    }


}