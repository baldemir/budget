<?php

namespace App\Http\Controllers\Integration;

use App\Account;
use App\ConnectedProvider;
use App\Http\Controllers\Controller;
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


    public function loginRedirectAlbaraka(Request $request)
    {
        try{
            $headers = [ 'content-type' => 'text/html', 'authorization'=>'Basic dDFranl1dWcyMno0MnljaGZ3dGd2aGllcTZ3aXVxdjU6RmkhI2tQRno0Ljd3emVaNW44Z05jUXRWKiV0ZFl6Y0dzc1o5IT00NVQkOGpnMDFqUGphM3dvaXV2NWQ9cVZWeQ=='];
            $body['grant_type'] = "authorization_code";
            //$body['client_id'] = "t1kjyuug22z42ychfwtgvhieq6wiuqv5";
            //$body['client_secret'] = "Fi!#kPFz4.7wzeZ5n8gNcQtV*%tdYzcGssZ9!=45T$8jg01jPja3woiuv5d=qVVy";
            $body['code'] = $request->get("code");
            $body['redirect_uri'] = 'http://budget.test/loginRedirectAlbaraka';

            $result = $this->postGuzzleRequest($headers, "https://apitest.albarakaturk.com.tr/ocf-auth-server/auth/oauth/token", $body);

            if($result->access_token == null){
                return Result::failureResponse(Result::$FAILURE_PROCESS, "Can't get the access token from provider.");
            }
            $currentProvider = Provider::where('alias', 'albaraka')->first();
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

    public function printAlbarakaAccounts()
    {
        $user = Auth::user();
        $provider = $user->connectedProviders[1];
        var_dump(self::getAlbarakaAccounts($provider->access_token, $provider->client_id, $provider->client_secret));
    }

    public function printIsbankAccounts()
    {
        $user = Auth::user();
        $provider = $user->connectedProviders[0];
        var_dump(self::getIsbankAccounts($provider->access_token, "d108aace-b5a0-486a-a024-e8b5c26f22ce", "A3cF1nA5yC1nK0kR5jX5hJ0fV2bX7eJ2vS0pV8jQ1mX3wY3gU6"));
    }

    public static function getAlbarakaAccounts($token, $clientId, $clientSecret){
        $headers = ['authorization' => 'Bearer ' . $token, 'x-ibm-client-id' => $clientId , 'x-ibm-client-secret' => $clientSecret];
        $body = ["context"=>"channel"];
        return self::getGuzzleRequest($headers, 'https://apitest.albarakaturk.com.tr/api/accounts/v1/list', $body);
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
        $response = $client->post($url, array('form_params' => $body, 'verify'=> false));

        $response = json_decode($response->getBody());


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
