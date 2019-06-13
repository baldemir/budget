<?php


namespace App\Http\Controllers\API;


use App\Space;
use App\Spending;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;


class LoginController extends BaseController
{

    public function extensionLogin(Request $request){
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
        $uye = User::where('email', $request->email)->first();

        if (isset($uye)) {
            if (Auth::guard('web')->attempt($credentials)){
                if($uye->api_token ==null){
                    $uye->api_token = $this->generateToken();
                    $uye->save();
                }
                return $this->sendResponse($uye, 'Logged in successfully.');
            }else{
                return $this->sendError('Authentication Error 1.');
            }
        }else{
            return $this->sendError('Authentication Error.');
        }

    }

    function generateToken(){
        return bin2hex(random_bytes(20));
    }

}