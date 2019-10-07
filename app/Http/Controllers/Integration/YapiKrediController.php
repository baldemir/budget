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

use Auth;
use Illuminate\Support\Facades\Lang;

class YapiKrediController extends Controller {
    /**
     * @bodyParam token string required Token acquired from facebook login attempt.
     */
    public function loginRedirectYapi(Request $request)
    {
        try{
            $currentProvider = Provider::where('alias', 'yapi')->first();
            if($currentProvider == null){
                return Result::failureResponse(Result::$FAILURE_DB, "Can't find the provider.");
            }

            $headers = ['accept' => 'application/json', 'content-type' => 'application/x-www-form-urlencoded', 'user-agent' => ''];
            $body['scope'] = "oob";
            $body['grant_type'] = "client_credentials";
            $body['client_id'] = $currentProvider->client_id;
            $body['client_secret'] = $currentProvider->client_secret;

            $result = $this->postGuzzleRequest($headers, "https://api.yapikredi.com.tr/auth/oauth/v2/token", $body);
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

    public function printAlbarakaAccounts()
    {
        $user = Auth::user();
        $provider = $user->connectedProviders[1];
        var_dump(self::getAlbarakaAccounts($provider->access_token, $provider->client_id, $provider->client_secret));
    }



    public static function getAlbarakaAccounts($token, $clientId, $clientSecret){
        $headers = ['authorization' => 'Bearer ' . $token, 'x-ibm-client-id' => $clientId , 'x-ibm-client-secret' => $clientSecret];
        $body = ["context"=>"channel"];
        return self::getGuzzleRequest($headers, 'https://apitest.albarakaturk.com.tr/api/accounts/v1/list', $body);
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
