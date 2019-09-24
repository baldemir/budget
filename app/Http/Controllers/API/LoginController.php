<?php


namespace App\Http\Controllers\API;


use App\Mail\VerifyRegistration;
use App\Result;
use App\Space;
use App\Tag;
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
                $this->createDefaultCategories($createdUser, $request->get('deviceLanguage'));
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

    public function createDefaultCategories($user, $lang="tr"){


        $defaultCategoriesTr = array(
            ["name" => "Market", "color" => Tag::randomColor(), "image" => "icon-category-grocery.png"],
            ["name" => "Giyim / Aksesuar", "color" => Tag::randomColor(), "image" => "icon-category-shopping.png"],
            ["name" => "Akaryakıt", "color" => Tag::randomColor(), "image" => "icon-category-auto.png"],
            ["name" => "Yeme / İçme", "color" => Tag::randomColor(), "image" => "icon-category-food.png"],
            ["name" => "Fatura", "color" => Tag::randomColor(), "image" => "icon-category-office.png"],
            ["name" => "Kira", "color" => Tag::randomColor(), "image" => "icon-category-rent.png"],
            ["name" => "Aidat", "color" => Tag::randomColor(), "image" => "icon-category-bank.png"],
            ["name" => "Seyahat", "color" => Tag::randomColor(), "image" => "icon-category-travel.png"],
            ["name" => "Eğitim", "color" => Tag::randomColor(), "image" => "icon-category-education.png"],
            ["name" => "Ulaşım", "color" => Tag::randomColor(), "image" => "icon-category-transport.png"],
            ["name" => "Eğlence / Hobi", "color" => Tag::randomColor(), "image" => "icon-category-entertainment.png"],
            ["name" => "Sağlık", "color" => Tag::randomColor(), "image" => "icon-category-medical.png"],
            ["name" => "Hediye", "color" => Tag::randomColor(), "image" => "icon-category-gift.png"],
            ["name" => "Kişisel Bakım", "color" => Tag::randomColor(), "image" => "icon-category-pet.png"],
            ["name" => "Vergi", "color" => Tag::randomColor(), "image" => "icon-category-tax.png"],
            ["name" => "Diğer", "color" => Tag::randomColor(), "image" => "icon-category-misc.png"]);

        $defaultCategoriesEn = array(
            ["name" => "Groceries", "color" => Tag::randomColor(), "image" => "icon-category-grocery.png"],
            ["name" => "Clothing", "color" => Tag::randomColor(), "image" => "icon-category-shopping.png"],
            ["name" => "Fuel", "color" => Tag::randomColor(), "image" => "icon-category-auto.png"],
            ["name" => "Food", "color" => Tag::randomColor(), "image" => "icon-category-food.png"],
            ["name" => "Utilities", "color" => Tag::randomColor(), "image" => "icon-category-office.png"],
            ["name" => "Housing", "color" => Tag::randomColor(), "image" => "icon-category-rent.png"],
            ["name" => "Fee", "color" => Tag::randomColor(), "image" => "icon-category-bank.png"],
            ["name" => "Travel", "color" => Tag::randomColor(), "image" => "icon-category-travel.png"],
            ["name" => "Education", "color" => Tag::randomColor(), "image" => "icon-category-education.png"],
            ["name" => "Transportation", "color" => Tag::randomColor(), "image" => "icon-category-transport.png"],
            ["name" => "Entertainment", "color" => Tag::randomColor(), "image" => "icon-category-entertainment.png"],
            ["name" => "Healthcare", "color" => Tag::randomColor(), "image" => "icon-category-medical.png"],
            ["name" => "Gift", "color" => Tag::randomColor(), "image" => "icon-category-gift.png"],
            ["name" => "Personal Care", "color" => Tag::randomColor(), "image" => "icon-category-pet.png"],
            ["name" => "Tax", "color" => Tag::randomColor(), "image" => "icon-category-tax.png"],
            ["name" => "Misc", "color" => Tag::randomColor(), "image" => "icon-category-misc.png"]);


        $spaceId = $user->spaces()->first()->id;
        $cats=$defaultCategoriesTr;
        if($lang != "tr"){
            $cats = $defaultCategoriesEn;
        }
        foreach ($cats as $userTag){

            Tag::create([
                'space_id' => $spaceId,
                'name' => $userTag["name"],
                'color' => $userTag["color"],
                'image' => str_replace('/storage/category/', '', $userTag["image"])
            ]);
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
