<?php


namespace App\Http\Controllers\API;


use App\Mail\VerifyRegistration;
use App\Result;
use App\Space;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Laravel\Socialite\Facades\Socialite;
use Validator;


class LoginController extends BaseController
{
    /**
     * @bodyParam email string required Email of user.
     * @bodyParam password string required Password of user.
     */
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
                return $this->responseObject($uye);
            }else{
                return $this->failureResponse(Result::$FAILURE_AUTH_WRONG, 'Authentication Error 1.');
            }
        }else{
            return $this->failureResponse(Result::$FAILURE_AUTH_WRONG, 'Authentication Error 2.');
        }

    }

    function generateToken(){
        return bin2hex(random_bytes(20));
    }

    /**
     * @bodyParam token string required Token acquired from facebook login attempt.
     */
    public function loginWithFacebookAccessToken(Request $request)
    {
        try{
            $user = Socialite::driver('facebook')->userFromToken($request->get('token'));
            $userModel = new User();
            $create['name'] = $user->getName();
            $create['email'] = $user->getEmail();
            $create['facebook_id'] = $user->getId();
            $existingUser = User::where('email', $user->getEmail())->first();
            if($existingUser == null){
                $createdUser = $userModel->addNew($create);
                // Space
                $space = new Space;
                $space->currency_id = 1;
                $space->name = $createdUser->name . '\'s Space';
                $space->save();
                $createdUser->spaces()->attach($space->id, ['role' => 'admin']);
                Mail::to($createdUser->email)->queue(new VerifyRegistration($createdUser));
            }else{
                $existingUser->facebook_id = $user->getId();
                $existingUser->save();
                $createdUser = $existingUser;
            }
            if($createdUser->api_token ==null){
                $createdUser->api_token = $this->generateToken();
                $createdUser->save();
            }
            return $this->responseObject($createdUser);
        }catch (Exception $e){
            return $this->failureResponse(Result::$FAILURE_PROCESS, $e->getMessage());
        }
    }

    function responseObject($result){
        $res = Result::$SUCCESS->setContent($result);
        return response()->json($res, 200, [], JSON_NUMERIC_CHECK);
    }

    function failureResponse($result, $content){
        $res = $result->setContent($content);
        return response()->json($res, 200, [], JSON_NUMERIC_CHECK);
    }

}