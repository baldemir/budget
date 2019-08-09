<?php

namespace App\Http\Controllers;

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




    public function postGuzzleRequest($headers, $url, $body)
    {
        //$client = new \GuzzleHttp\Client(['headers' => ['X-Foo' => 'Bar']]);
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->post($url,  array('form_params'=>$body));

        $response = json_decode($response->getBody());


        return $response;
    }



}
