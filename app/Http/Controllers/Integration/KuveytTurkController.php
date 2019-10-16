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

        self::importAccounts($provider->access_token);
    }

    public static function signData($accessToken, $queryString){
        //data you want to sign
        $data = trim($accessToken) . $queryString;

        $pkeyid = openssl_pkey_get_private("file://kuveyt_private_key.pem");


        openssl_sign($data, $signature, $pkeyid, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * @return string
     * @desc Local signature yaratmak için eklenmiştir.
     * @deprecated SİLİNECEK
     */
    public function createSignature(){

        $accessToken = "03a65bad19804caff2056188ccac237a5a56a5568257088d63442491da67b90c";
        $queryString = "?scope=accounts";

        $postbody=false;
        if($postbody){
            $queryString ="?grant_type=refresh_token&refresh_token=5d851583751e876901fb175f938a90d18bccbbe421598f1ae93f63260b7ba7f6";
        }

        return $this->signData($accessToken,$queryString);
    }


    /**
     * @desc  This function updates access_token and refresh_token
     *
     */
    public function refreshToken(){

        // GET connected_providers of user
        $user = Auth::user();
        $connectedProvider = $user->connectedProviders->where('provider_id', 4)->first();
        // $connectedProvider = ConnectedProvider::find($connectedProviderId);

        if($connectedProvider == null){
            return Result::failureResponse(Result::$FAILURE_DB, "Can't find the connected provider.");
        }

        // GET providers clientId & clientSecret
        $currentProvider = Provider::where('alias', 'kuveyt')->first();
        $clientId = $currentProvider->client_id;
        $clientSecret = $currentProvider->client_secret;

        $headers = [];
        $body['grant_type'] = "refresh_token";
        $body['refresh_token'] = $connectedProvider->refresh_token;
        $body['client_id'] = $clientId;
        $body['client_secret'] = $clientSecret;

        // REQUEST ACCESS & REFRESH TOKENS
        $result = $this->postGuzzleRequest($headers, "https://idprep.kuveytturk.com.tr/api/connect/token", $body);

        if($result->access_token == null){
            return Result::failureResponse(Result::$FAILURE_PROCESS, "Can't get the access token from provider.");
        }

        $connectedProvider->access_token = $result->access_token;
        $connectedProvider->expiry_time = Carbon::now()->addSeconds($result->expires_in)->toDateTimeString();
        $connectedProvider->refresh_token = $result->refresh_token;
        $connectedProvider->save();

        // Return JSON
        echo json_encode($connectedProvider);die;
    }


    /**
     * @desc  This function imports account transactions
     */
    public function importAllAccountTransaction(){

        // GET connected_providers of user
        $user = Auth::user();
        $connectedProvider = $user->connectedProviders->where('provider_id', 4)->first();
        $token = $connectedProvider->access_token;

        // GET user provider accounts
        $accountList = Account::where('provider_id' , 4)->get();
        foreach ($accountList as $account){
            $suffix = $account->account_suffix;
            $path = "https://apitest.kuveytturk.com.tr/prep/v1/accounts/".$suffix."/transactions";
            $queryString= "?onlyOpen=true&onlyWithNoBalance=false&onlyCurrent=true&sharedWithMultiSignature=true";
            $headers = [
                'Authorization' => 'Bearer ' . $token ,
                'Signature' => $this->signData($token, $queryString)
            ];
            $body = [];

            // GET Account Transactions
            $result = self::getGuzzleRequest($headers, $path . $queryString, $body);

            foreach ($result->value as $transaction){

                // kuveytturk account transaction düzgün gelmediğinden kontrol eklenmiştir
                if($transaction->suffix==$suffix){

                    // Spending Transactions
                    if($transaction->amount<0){
                        $newTransaction = new Spending();
                        $newTransaction->description = $transaction->description;
                        $newTransaction->amount = $transaction->amount;
                        $newTransaction->account_id = $account->id;
                        $newTransaction->space_id = $account->space_id;
                        $newTransaction->happened_on = Carbon::createFromTimestamp( strtotime($transaction->date))->toDateTimeString();
                        $newTransaction->save();
                    // Earning Transactions
                    }else{
                        $newTransaction = new Earning();
                        $newTransaction->description = $transaction->description;
                        $newTransaction->amount = $transaction->amount;
                        $newTransaction->account_id = $account->id;
                        $newTransaction->space_id = $account->space_id;
                        $newTransaction->happened_on = Carbon::createFromTimestamp( strtotime($transaction->date))->toDateTimeString();
                        $newTransaction->save();
                    }
                }
            }
        }
        echo \GuzzleHttp\json_encode($accountList);die;

    }


    public function importAccounts($token){

        $currentProvider = Provider::where('alias', 'kuveyt')->first();
        $currentSpace = Space::find(session('space')->id);
        $userCurrentAccounts = $currentSpace->accounts->where('provider_id',  $currentProvider->id);

        $path = "https://apitest.kuveytturk.com.tr/prep/v1/accounts";
        $queryString= "?onlyOpen=true&onlyWithNoBalance=false&onlyCurrent=true&sharedWithMultiSignature=true";

        $headers = ['authorization' => 'Bearer ' . $token,'Signature' => $this->signData($token, $queryString)];
        $body = ["context"=>"channel"];
        $result = self::getGuzzleRequest($headers, $path . $queryString, $body);

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
            $newAccount->account_suffix= $account->suffix;
            $newAccount->save();
        }

        echo \GuzzleHttp\json_encode($result);die;
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
