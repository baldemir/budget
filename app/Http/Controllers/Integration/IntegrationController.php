<?php

namespace App\Http\Controllers\Integration;

use App\Account;
use App\ConnectedProvider;
use App\Earning;
use App\Http\Controllers\Controller;
use App\Provider;
use App\Result;
use App\Space;
use App\Spending;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

use Auth;
use Illuminate\Support\Facades\Lang;

class IntegrationController extends Controller {

    public static $providerId=5;
    public static $access_token_url="https://api.sandbox.isbank.com.tr/v1/sandbox/oauth2/token";

    /**
     * @bodyParam token string required Token acquired from facebook login attempt.
     * @desc        This function get access_token
     */
    public function loginRedirectIsbank(Request $request)
    {
        try{

            $isBankProvider=Provider::find(self::$providerId);
            $body['grant_type'] = "authorization_code";
            $body['client_id'] = $isBankProvider->client_id;
            $body['client_secret'] = $isBankProvider->client_secret;
            $body['code'] = $request->get("code");
            $headers = [];

            $result = $this->postGuzzleRequest($headers, self::$access_token_url, $body);
            if($result->access_token == null){
                return Result::failureResponse(Result::$FAILURE_PROCESS, "Can't get the access token from provider.");
            }

            $user = Auth::user();
            $connectedProvider = new ConnectedProvider();
            $alreadyConnectedProvider = $user->connectedProviders()->where('provider_id', self::$providerId)->first();
            if($alreadyConnectedProvider  != null){
                $connectedProvider = $alreadyConnectedProvider;
            }
            $connectedProvider->user_id = $user->id;
            $connectedProvider->provider_id = self::$providerId;
            $connectedProvider->access_token = $result->access_token;
            $connectedProvider->expiry_time = Carbon::now()->addSeconds($result->expires_in)->toDateTimeString();
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


    /**
     * @desc  This function imports account transactions
     */
    public function importAccountTransactions($accountId){

        // Check Access Token Expiry Date
        // self::checkAccessTokenExpiryDate();

        $account = Account::find($accountId);
        if($account == null){
            return Result::failureResponse(Result::$FAILURE_DB, "Can't find the account by given accountId");
        }

        if($account->status==1){

            // GET connected_providers of user
            $user = Auth::user();
            $connectedProvider = $user->connectedProviders->where('provider_id', self::$providerId)->first();
            $token = $connectedProvider->access_token;

            $isBankProvider=Provider::find(self::$providerId);
            $providerAccountId = $account->provider_account_id;
            $path="https://api.sandbox.isbank.com.tr/v1/accounts/".$providerAccountId."/transactions";
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'X-IBM-client-id' => $isBankProvider->client_id,
                'X-IBM-client-secret' => $isBankProvider->client_secret
            ];
            $body = [];
            $result = self::getGuzzleRequest($headers, $path, $body);

            foreach ($result->data as $transaction){
                // account transaction düzgün gelmediğinden kontrol eklenmiştir
                try{
                    $newTransaction = $transaction->amount<0 ? new Spending() : new Earning();
                    $newTransaction->account_id = $account->id;
                    $newTransaction->space_id = $account->space_id;
                    $newTransaction->description = $transaction->description;
                    $newTransaction->amount = $transaction->amount;
                    $newTransaction->happened_on = Carbon::createFromTimestamp( strtotime($transaction->time))->toDateTimeString();
                    $newTransaction->real_id = $transaction->id;
                    $newTransaction->save();
                }catch (\Exception $e){
                    //return Result::failureResponse(Result::$FAILURE_DB, "Duplicate entry error >> importAccountTransactions");
                }
            }
        }
        echo \GuzzleHttp\json_encode($result);die;
    }


    public function importAccounts($token){
        // Check Access Token Expiry Date
        //self::checkAccessTokenExpiryDate();

        $isBankProvider=Provider::find(self::$providerId);
        $path = "https://api.sandbox.isbank.com.tr/v1/accounts";
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'X-IBM-client-id' => $isBankProvider->client_id,
            'X-IBM-client-secret' => $isBankProvider->client_secret
        ];
        $body = [];
        $result = self::getGuzzleRequest($headers, $path, $body);

        $providerAccountIds = [];
        $currentSpace = Space::find(session('space')->id);
        $userCurrentAccounts = $currentSpace->accounts->where('provider_id',  self::$providerId);
        foreach ($userCurrentAccounts as $currentAccount){
            $providerAccountIds[] = $currentAccount->real_id;
        }
        foreach($result->data as $account){

            if(in_array($account->iban, $providerAccountIds)){
                continue;
            }

            try{
                $newAccount = new Account();
                $newAccount->space_id = session('space')->id;
                $newAccount->name = $account->account_number;
                $newAccount->color = Account::randomColor();
                $newAccount->provider_id = self::$providerId;

                switch($account->currency_code){
                    case 'EUR':
                        $newAccount->currency_id = 1;
                        break;
                    default:
                        $newAccount->currency_id = 0;
                        break;
                }

                $newAccount->real_id = $account->iban;
                $newAccount->real_name = '-';
                $newAccount->balance = $account->account_balance;
                $newAccount->available_balance = $account->available_amount;
                //$newAccount->open_date = $account->openDate;
                $newAccount->branch_name= $account->branch_name;
                $newAccount->provider_account_id = $account->account_id;
                $newAccount->save();
            }catch (\Exception $e){
                // TODO:
                // Burada hata kodu append edilecek
            }
        }

        echo \GuzzleHttp\json_encode($result);die;
    }


    public function printAccounts(){
        $user = Auth::user();
        $provider = $user->connectedProviders->where('provider_id', self::$providerId)->first();
        self::importAccounts($provider->access_token);
    }



    public static function postGuzzleRequest($headers, $url, $body)
    {
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->post($url, array('form_params' => $body, 'verify'=> false));
        $response = json_decode($response->getBody());
        return $response;
    }

    public static function getGuzzleRequest($headers, $url, $body)
    {
        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $response = $client->get($url,  array('form_params'=>$body, 'verify'=> false));
        $response = json_decode($response->getBody());
        return $response;
    }



}
