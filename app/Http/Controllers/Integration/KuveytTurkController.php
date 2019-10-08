<?php

namespace App\Http\Controllers\Integration;

use App\Account;
use App\ConnectedProvider;
use App\Http\Controllers\Controller;
use App\Provider;
use App\Result;
use App\Space;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

class KuveytTurkController extends Controller {
    /**
     * @bodyParam token string required Token acquired from facebook login attempt.
     */
    public function loginRedirectKuveyt(Request $request)
    {
        try{
            $currentProvider = Provider::where('alias', 'kuveyt')->first();
            if($currentProvider == null){
                return Result::failureResponse(Result::$FAILURE_DB, "Can't find the provider.");
            }

            $headers = [];
            $body['grant_type'] = "authorization_code";
            $body['redirect_uri'] = "http://budget.test/loginRedirectKuveyt";
            $body['client_id'] = $currentProvider->client_id;
            $body['client_secret'] = $currentProvider->client_secret;
            $body['code'] = $request->get("code");

            $result = $this->postGuzzleRequest($headers, "https://idprep.kuveytturk.com.tr/api/connect/token", $body);

            if($result->access_token == null){
                return Result::failureResponse(Result::$FAILURE_PROCESS, "Can't get the access token from provider.");
            }

            $user = Auth::user();
            $connectedProvider = new ConnectedProvider();
            $alreadyConnectedProvider = $user->connectedProviders()->where('provider_id', $currentProvider->id)->first();
            if($alreadyConnectedProvider  != null){
                $connectedProvider = $alreadyConnectedProvider;
            }
            $connectedProvider->user_id = $user->id;
            $connectedProvider->provider_id = $currentProvider->id;
            $connectedProvider->access_token = $result->access_token;
            $connectedProvider->expiry_time = Carbon::now()->addSeconds($result->expires_in)->toDateTimeString();
            try {
                $connectedProvider->refresh_token = $result->refresh_token;
            }catch (\Exception $e){
                //no refresh token
            }
            $connectedProvider->save();

            return redirect()
                ->route('dashboard')
                ->with([
                    'alert_type' => 'success',
                    'alert_message' => Lang::get("integration.bank_connected_successfully")
                ]);

        }catch (ClientException $e){
            return Result::failureResponse(Result::$FAILURE_PROCESS, $e->getMessage());
        }
    }

    public function printAccounts()
    {
        $user = Auth::user();
        $provider = $user->connectedProviders->where('provider_id', 4)->first();

        var_dump(self::importAccounts($provider->access_token, $provider->client_id, $provider->client_secret));
    }

    public static function signData($accessToken, $queryString){
        //data you want to sign
        $data = trim($accessToken) . $queryString;

        $pkeyid = openssl_pkey_get_private("file://kuveyt_private_key.pem");


        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }


    public function importAccounts($token, $clientId, $clientSecret){
        $user = Auth::user();

        $currentProvider = Provider::where('alias', 'kuveyt')->first();
        $currentSpace = Space::find(session('space')->id);
        $userCurrentAccounts = $currentSpace->accounts->where('provider_id',  $currentProvider->id);

        $path = "https://apitest.kuveytturk.com.tr/prep/v1/accounts";
        $queryString= "?onlyOpen=true&onlyWithNoBalance=false&onlyCurrent=true&sharedWithMultiSignature=true";

        $headers = ['authorization' => 'Bearer ' . $token,'Signature' => $this->signData($token, $queryString)];
        $body = ["context"=>"channel"];
        $result = self::getGuzzleRequest($headers, $path . $queryString, $body);
        var_dump($result->value);
        $providerAccountIds = [];
        foreach ($userCurrentAccounts as $currentAccount){
            $providerAccountIds[] = $currentAccount->real_id;
        }
        foreach($result->value as $account){

            if(in_array($account->iban, $providerAccountIds)){
                continue;
            }
            $newAccount = new Account();
            $newAccount->space_id = session('space')->id;
            if($account->name){
                $newAccount->name = $account->name;
            }else{
                $newAccount->name = $currentProvider->name;
            }
            $newAccount->color = Account::randomColor();
            $newAccount->provider_id = $currentProvider->id;
            switch($account->fxId){
                case 0:
                    $newAccount->currency_id = 1;
                    break;
                case 1:
                    $newAccount->currency_id = 2;
                    break;
                default:
                    $newAccount->currency_id = 0;
                    break;
            }

            $newAccount->real_id = $account->iban;
            $newAccount->real_name = $account->name;
            $newAccount->balance = $account->balance;
            $newAccount->available_balance = $account->availableBalance;
            $newAccount->open_date = $account->openDate;
            $newAccount->branch_name= $account->branchName;
            $newAccount->save();
        }
    }



    public static function synchronizeIsbankTransactions($accountId){
        $user = Auth::user();
        $account = Account::find($accountId);
        $provider = $account->provider()->first();
        $connectedProvider = $provider->connectedProviders()->where('user_id', $user->id)->first();
        return self::getIsbankAccounts($connectedProvider->access_token, $provider->client_id, $provider->client_secret);
        $headers = ['authorization' => 'Bearer ' . $connectedProvider->access_token, 'x-ibm-client-id' => $provider->client_id, 'x-ibm-client-secret' => $provider->client_secret];
        $body = [];
        return Result::responseObject(self::getGuzzleRequest($headers, $account->sync_url, $body));
    }




    public static function postGuzzleRequest($headers, $url, $body)
    {
        //$client = new \GuzzleHttp\Client(['headers' => ['X-Foo' => 'Bar']]);
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->post($url, array('form_params' => $body));

        $response = json_decode($response->getBody()->getContents());


        return $response;
    }

    public static function getGuzzleRequest($headers, $url, $body)
    {
        //$client = new \GuzzleHttp\Client(['headers' => ['X-Foo' => 'Bar']]);
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->get($url,  array('form_params'=>$body, 'verify'=> false));

        $response = json_decode($response->getBody());


        return $response;
    }



}
