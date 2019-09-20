<?php

namespace App\Http\Controllers;

use App\Account;
use App\ConnectedProvider;
use App\Provider;
use App\Result;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

use App\Spending;
use App\Tag;

use Auth;
use Illuminate\Support\Facades\Lang;

class IntegrationController extends Controller {
    /**
     * @bodyParam token string required Token acquired from facebook login attempt.
     */
    public function loginRedirectIsbank(Request $request)
    {
        try{
            $headers = ['accept' => 'application/x-www-form-urlencoded', 'content-type' => 'text/html'];
            $body['grant_type'] = "authorization_code";
            $body['client_id'] = "d108aace-b5a0-486a-a024-e8b5c26f22ce";
            $body['client_secret'] = "A3cF1nA5yC1nK0kR5jX5hJ0fV2bX7eJ2vS0pV8jQ1mX3wY3gU6";
            $body['code'] = $request->get("code");

            $result = $this->postGuzzleRequest($headers, "https://api.sandbox.isbank.com.tr/v1/sandbox/oauth2/token", $body);
            if($result->access_token == null){
                return Result::failureResponse(Result::$FAILURE_PROCESS, "Can't get the access token from provider.");
            }
            $currentProvider = Provider::where('alias', 'isbank')->first();
            if($currentProvider == null){
                return Result::failureResponse(Result::$FAILURE_DB, "Can't find the provider.");
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
            /*
            $userAccounts = self::getIsbankAccounts($connectedProvider->access_token, $currentProvider->client_id, $currentProvider->client_secret)->data;
            foreach ($userAccounts as $userAccount){
                $newAccount = new Account();
                if(Account::where('real_id', $userAccount->account_id)->where('space_id', session('space')->id)->first() != null){
                    continue;
                }else{
                    $newAccount->space_id = session('space')->id;
                    $newAccount->name = $currentProvider->name;
                    $newAccount->provider_id = $currentProvider->id;
                    $newAccount->currency_id = 0;
                    $newAccount->sync_url = "https://api.sandbox.isbank.com.tr/v1/accounts/" . $userAccount->account_id . "/transactions";
                    $newAccount->real_id = $userAccount->account_id;
                    $newAccount->save();
                }
            }
            */
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

    public static function getIsbankAccounts($token, $clientId, $clientSecret){
        $headers = ['authorization' => 'Bearer ' . $token, 'x-ibm-client-id' => $clientId , 'x-ibm-client-secret' => $clientSecret];
        $body = [];
        return self::getGuzzleRequest($headers, 'https://api.sandbox.isbank.com.tr/v1/accounts', $body);
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
        $response = $client->post($url,  array('form_params'=>$body));

        $response = json_decode($response->getBody());


        return $response;
    }

    public static function getGuzzleRequest($headers, $url, $body)
    {
        //$client = new \GuzzleHttp\Client(['headers' => ['X-Foo' => 'Bar']]);
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->get($url,  array('form_params'=>$body));

        $response = json_decode($response->getBody());


        return $response;
    }



}
