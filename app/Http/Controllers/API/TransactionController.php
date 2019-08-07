<?php


namespace App\Http\Controllers\API;


use App\Earning;
use App\Repositories\TagRepository;
use App\Repositories\TransactionRepository;
use App\Result;
use App\Space;
use App\Spending;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Validator;
use Image;


class TransactionController extends BaseController
{

    public function __construct(TransactionRepository $transactionRepository) {
        $this->repository = $transactionRepository;
    }

    public function rand_color() {
        return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Spending::all();


        return $this->sendResponse($products->toArray(), 'Products retrieved successfully.');
    }


    /**
     * @bodyParam tag_id int required Category id of transaction.
     * @bodyParam happened_on string required Date of transaction in format of YYY-mm-dd.
     * @bodyParam description string required The description(name) of transaction.
     * @bodyParam amount int required The amount of transaction.
     */
    public function saveTransaction(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'tag_id' => 'nullable|exists:tags,id', // TODO CHECK IF TAG BELONGS TO USER
            'happened_on' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/',
            'account_id' => 'nullable|exists:accounts,id', // TODO CHECK IF account BELONGS TO USER
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = Auth::guard('api')->user();
        $spaceId = $user->spaces()->first()->id;
        $input["space_id"] = $spaceId;

        $input["amount"] = $input["amount"] * 100;

        if($input["type"] == 1){
            $product = Earning::create($input);
        }else{
            if($input["import_id"] == 0){
                $input = \array_diff_key($input, ["import_id" => "xy"]);
            }
            $product = Spending::create($input);
        }

        return $this->sendResponse($product->toArray(), 'Transaction created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Spending::find($id);
        if (is_null($product)) {
            return $this->sendError('Transaction not found.');
        }

        $user = Auth::guard('api')->user();

        $userOwnsSpace = Space::find($product->space_id)->users()->get()->contains($user->id);

        if(!$userOwnsSpace){
            return $this->sendError('Authentication Error.');
        }




        return $this->sendResponse($product->toArray(), 'Product retrieved successfully.');
    }


    /**
     * Update the specified transaction.
     *
     * @bodyParam tag_id int required Category id of transaction.
     * @bodyParam happened_on string required Date of transaction in format of YYY-mm-dd.
     * @bodyParam description string required The description(name) of transaction.
     * @bodyParam amount int required The amount of transaction.
     */
    public function update(Request $request, Spending $product)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();


        return $this->sendResponse($product->toArray(), 'Product updated successfully.');
    }


    /**
     * Remove the specified transaction from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Spending $product)
    {
        $product->delete();

        return $this->sendResponse($product->toArray(), 'Transaction deleted successfully.');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addGarantiTransactions(Request $request)
    {
        $user = Auth::guard('api')->user();
        $content = json_decode($request->get("transactions"));
        $savedTransaction = 0;
        $elemValue = null;
        try{
            $elemValue = $content->{array_keys((array)$content)[0]};
        }catch (\Exception $e){

        }

        $transactionsStarted = false;
        foreach ($elemValue as $elem){

            if(count($elem) == 5){

                if(!$transactionsStarted){
                    if($elem[0] == "Tarih"){
                        //this must be beginning of transactions
                        $transactionsStarted = true;
                    }else{
                        //sth is wrong
                        return $this->sendError('Format Error.');
                    }
                }else{

                    try{
                        if(\DateTime::createFromFormat('d/m/Y', $elem[0]) == false){
                            continue;
                        }

                        $amount = str_replace(',', '.', str_replace('.', '', $elem[4]));
                        $amount = intVal($amount * 100);
                        $transaction = null;
                        $isNegative = ($request->get("spendingNegative") === 'true');
                        if( $isNegative == false){
                            $amount = $amount * -1;
                        }

                        if($amount>0){
                            $transaction = new Earning();
                        }else{
                            $transaction = new Spending();
                        }

                        $spaceId = $user->spaces()->first()->id;

                        $transaction->happened_on = \DateTime::createFromFormat('d/m/Y', $elem[0])->format('Y-m-d');
                        $transaction->description = $elem[1];
                        $tag = Tag::where('name', $elem[2])->where('space_id', $spaceId)->first();
                        if($tag == null){

                            $tag = new Tag();
                            $tag->name = $elem[2];
                            $tag->color= $this->rand_color();
                            $tag->space_id = $spaceId;
                            $tag->save();
                        }
                        $isDuplicate = true;

                        $transaction->space_id = $spaceId;
                        if($amount <= 0){
                            $transaction->tag_id = $tag->id;
                            $transaction->account_id = 1;
                            $amount = $amount * -1;
                            $isDuplicate = $this->getDuplicateSpending($transaction->description, $transaction->happened_on, $transaction->space_id, $transaction->tag_id, $amount);
                        }else{
                            $isDuplicate = $this->getDuplicateEarning($transaction->description, $transaction->happened_on, $transaction->space_id, $amount);
                        }
                        $transaction->amount = $amount;




                        if($isDuplicate != null){
                            continue;
                        }else{

                        }



                        $transaction->save();

                        $savedTransaction++;
                    }catch (\Exception $exception){
                        return $this->sendError('Validation Error.', $exception->getMessage());
                    }
                }
            }
        }
        return $savedTransaction;
    }

    public function getDuplicateSpending($description, $happened_on, $space_id, $tag_id, $amount){
        $isDuplicate = Spending::where('description', $description)
            ->where('happened_on', $happened_on)
            ->where('space_id', $space_id)
            ->where('amount', $amount)
            ->withTrashed()
            ->first();
        return $isDuplicate;
    }

    public function getDuplicateEarning($description, $happened_on, $space_id, $amount){
        $isDuplicate = Earning::where('description', $description)
            ->where('happened_on', $happened_on)
            ->where('space_id', $space_id)
            ->where('amount', $amount)
            ->withTrashed()
            ->first();
        return $isDuplicate;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addCeptetebTransactions(Request $request)
    {
        $user = Auth::guard('api')->user();
        $content = json_decode($request->get("transactions"));
        $savedTransaction = 0;
        $elemValue = null;

        $transactionsStarted = false;
        foreach ($content as $elem){

            try{
                try{
                    if($elem->islemtarihi != null){
                        $elem->tarih = $elem->islemtarihi;
                    }
                    if($elem->islemtutari != null){
                        $elem->tutar = $elem->islemtutari;
                    }
                }catch (\Exception $exception){

                }
                if(\DateTime::createFromFormat('d/m/Y', $elem->tarih) == false){
                    continue;
                }

                $amount = str_replace(',', '.', str_replace('.', '', $elem->tutar));
                $amount = intVal($amount * 100);
                $transaction = null;
                $isNegative = ($request->get("spendingNegative") === 'true');
                if( $isNegative == false){
                    $amount = $amount * -1;
                }
                if($amount>0){
                    $transaction = new Earning();
                }else{
                    $transaction = new Spending();
                }

                $spaceId = $user->spaces()->first()->id;

                $transaction->happened_on = \DateTime::createFromFormat('d/m/Y', $elem->tarih)->format('Y-m-d');
                $transaction->description = $elem->aciklama;
                $tag = Tag::where('name', "-")->where('space_id', $spaceId)->first();
                if($tag == null){

                    $tag = new Tag();
                    $tag->name = "-";
                    $tag->color= $this->rand_color();
                    $tag->space_id = $spaceId;
                    $tag->save();
                }
                $isDuplicate = true;

                $transaction->space_id = $spaceId;



                if($amount < 0){
                    $transaction->tag_id = $tag->id;
                    $transaction->account_id = 1;
                    $amount = $amount * -1;
                    $isDuplicate = $this->getDuplicateSpending($transaction->description, $transaction->happened_on, $transaction->space_id, $transaction->tag_id, $amount);

                }else{
                    $isDuplicate = $this->getDuplicateEarning($transaction->description, $transaction->happened_on, $transaction->space_id, $amount);

                }
                $transaction->amount = $amount;




                if($isDuplicate != null){
                    continue;
                }else{

                }
                $transaction->save();
                $savedTransaction++;
            }catch (\Exception $exception){
                return $this->sendError('Validation Error.', $exception->getMessage());
            }
        }

        return $savedTransaction;
    }
    public function turkishDateToNumeric($str){
        $str = str_replace('Oca', '/01/', $str);
        $str = str_replace('Şub', '/02/', $str);
        $str = str_replace('Mar', '/03/', $str);
        $str = str_replace('Nis', '/04/', $str);
        $str = str_replace('May', '/05/', $str);
        $str = str_replace('Haz', '/06/', $str);
        $str = str_replace('Tem', '/07/', $str);
        $str = str_replace('Ağu', '/08/', $str);
        $str = str_replace('Eyl', '/09/', $str);
        $str = str_replace('Eki', '/10/', $str);
        $str = str_replace('Kas', '/11/', $str);
        $str = str_replace('Ara', '/12/', $str);
        return $str;
    }
    public function addZiraatTransactions(Request $request)
    {
        $user = Auth::guard('api')->user();
        $content = json_decode($request->get("transactions"));
        $savedTransaction = 0;
        $elemValue = null;

        $transactionsStarted = false;
        foreach ($content as $elem){
            try{
                try{
                    if($elem->islemtutari != null){
                        $elem->tutar = $elem->islemtutari;
                    }
                }catch (\Exception $exception){

                }
                $elem->tarih = preg_replace('/\s+/', '', $elem->tarih);
                $elem->tarih = $this->turkishDateToNumeric($elem->tarih);

                if(\DateTime::createFromFormat('d/m/Y', $elem->tarih) == false){
                    continue;
                }

                $elem->tutar = preg_replace("/[^0-9,.+-]/", "", $elem->tutar);
                $amount = str_replace(',', '.', str_replace('.', '', $elem->tutar));
                $amount = str_replace('TL', '', $amount);
                if (strpos($amount, '+') !== false) {
                    $amount = $amount * -1;
                }
                $amount = intVal($amount * 100);
                $transaction = null;
                $isNegative = ($request->get("spendingNegative") === 'true');
                if( $isNegative == false){
                    $amount = $amount * -1;
                }
                if($amount>0){
                    $transaction = new Earning();
                }else{
                    $transaction = new Spending();
                }

                $spaceId = $user->spaces()->first()->id;

                $transaction->happened_on = \DateTime::createFromFormat('d/m/Y', $elem->tarih)->format('Y-m-d');
                $transaction->description = trim($elem->aciklama);
                $tag = Tag::where('name', Lang::get("general.other"))->where('space_id', $spaceId)->first();
                if($tag == null){

                    $tag = new Tag();
                    $tag->name = Lang::get("general.other");
                    $tag->color= $this->rand_color();
                    $tag->space_id = $spaceId;
                    $tag->save();
                }
                $isDuplicate = true;

                $transaction->space_id = $spaceId;


                DB::beginTransaction();

                if($amount < 0){
                    $transaction->tag_id = $tag->id;
                    $transaction->account_id = 1;
                    $amount = $amount * -1;
                    $isDuplicate = $this->getDuplicateSpending($transaction->description, $transaction->happened_on, $transaction->space_id, $transaction->tag_id, $amount);

                }else{
                    $isDuplicate = $this->getDuplicateEarning($transaction->description, $transaction->happened_on, $transaction->space_id, $amount);

                }
                $transaction->amount = $amount;




                if($isDuplicate != null){
                    DB::rollBack();
                    continue;
                }else{

                }
                $transaction->save();
                $savedTransaction++;
                DB::commit();
            }catch (\Exception $exception){
                return $this->sendError('Validation Error.', $exception->getMessage());
            }
        }

        return $savedTransaction;
    }

    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     */
    public function getMonthlySummary(Request $request){
        $user = Auth::guard('api')->user();

        $month = $request->input('month');
        $year = $request->input('year');

        $yearMonths = [];

        $day = 1;

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->earnings()->whereMonth('happened_on', '=',$month)->whereYear('happened_on', '=',$year)->groupBy('happened_on')->orderBy('happened_on')->selectRaw('SUM(amount) amount, DAY(happened_on) day')->get() as $earning) {
            $earning->type = 1;
            $earning->amount = $earning->amount/100;
            if($earning->day>$day){
                for($i=$day;$i<$earning->day;$i++){
                    $tempEarning = new \stdClass();
                    $tempEarning->day=$i;
                    $tempEarning->amount=0;
                    $tempEarning->type=1;
                    $yearMonths[] = $tempEarning;
                }
            }
            $day = $earning->day+1;
            $yearMonths[] = $earning;
        }

        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31

        if(count($yearMonths) > 0){
            if($yearMonths[count($yearMonths)- 1]->day < $numberOfDays){
                for($i=$yearMonths[count($yearMonths) - 1]->day+1 ;$i<=$numberOfDays;$i++){
                    $tempEarning = new \stdClass();
                    $tempEarning->day=$i;
                    $tempEarning->amount=0;
                    $tempEarning->type=1;
                    $yearMonths[] = $tempEarning;
                }
            }
        }else{
            for($i=1 ;$i<=$numberOfDays;$i++){
                $tempEarning = new \stdClass();
                $tempEarning->day=$i;
                $tempEarning->amount=0;
                $tempEarning->type=1;
                $yearMonths[] = $tempEarning;
            }
        }

        $monthlySpendings= [];
        $day = 1;

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->spendings()->whereMonth('happened_on', '=',$month)->whereYear('happened_on', '=',$year)->groupBy('happened_on')->orderBy('happened_on')->selectRaw('SUM(amount) amount, DAY(happened_on) day')->get() as $spending) {
            $spending->type = 1;
            $spending->amount = $spending->amount/100;
            if($spending->day>$day){
                for($i=$day;$i<$spending->day;$i++){
                    $tempSpending= new \stdClass();
                    $tempSpending->day=$i;
                    $tempSpending->amount=0;
                    $tempSpending->type=-1;
                    $monthlySpendings[] = $tempSpending;
                }
            }
            $day = $spending->day+1;
            $monthlySpendings[] = $spending;
        }

        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 31

        if(count($monthlySpendings) > 0) {
            if ($monthlySpendings[count($monthlySpendings) - 1]->day < $numberOfDays) {
                for ($i = $monthlySpendings[count($monthlySpendings) - 1]->day + 1; $i <= $numberOfDays; $i++) {
                    $tempSpending = new \stdClass();
                    $tempSpending->day = $i;
                    $tempSpending->amount = 0;
                    $tempSpending->type = -1;
                    $monthlySpendings[] = $tempSpending;
                }
            }
        }else{
            for($i=1 ;$i<=$numberOfDays;$i++){
                $tempSpending = new \stdClass();
                $tempSpending->day=$i;
                $tempSpending->amount=0;
                $tempSpending->type=-1;
                $monthlySpendings[] = $tempSpending;
            }
        }

        $result = new \stdClass();
        $result->spendings = $monthlySpendings;
        $result->earnings = $yearMonths;

        return $this->responseObject($result);
    }
    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     * @bodyParam day int required The specified day of month.
     */
    public function getDailyTransactions(Request $request){
        $user = Auth::guard('api')->user();
        $yearMonths = [];

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->earnings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->whereDay('happened_on', '=',$request->input('day'))->get() as $earning) {
            $earning->type = 1;
            $yearMonths[] = $earning;
        }
        foreach ($user->spaces()->first()->spendings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->whereDay('happened_on', '=',$request->input('day'))->get() as $earning) {
            $earning->type = -1;
            $earning->tag = $earning->tag()->first();
            $yearMonths[] = $earning;
        }
        usort($yearMonths, function ($a, $b) {
            return $a->happened_on < $b->happened_on;
        });

        return $this->responseObject($yearMonths);
    }

    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     */
    public function getMonthlyTransactions(Request $request){
        $user = Auth::guard('api')->user();
        $yearMonths = [];

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->earnings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->get() as $earning) {
            $earning->type = 1;
            $yearMonths[] = $earning;
        }
        foreach ($user->spaces()->first()->spendings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->get() as $earning) {
            $earning->type = -1;
            $earning->tag = $earning->tag()->first();
            $yearMonths[] = $earning;
        }
        usort($yearMonths, function ($a, $b) {
            return $a->happened_on < $b->happened_on;
        });

        return $this->responseObject($yearMonths);
    }

    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     * @bodyParam categoryId int required The specified category id.
     */
    public function getMonthlyTransactionsByCategory(Request $request){
        $user = Auth::guard('api')->user();
        $yearMonths = [];
        // Populate yearMonths with earnings
        /*
        foreach ($user->spaces()->first()->earnings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->get() as $earning) {
            $earning->type = 1;
            $yearMonths[] = $earning;
        }
        */
        foreach ($user->spaces()->first()->spendings()->where('tag_id', $request->get('categoryId'))->whereMonth('happened_on', '=',$request->get('month'))->whereYear('happened_on', '=',$request->get('year'))->get() as $spending) {
            $spending->type = -1;
            $spending->tag = $spending->tag()->first();
            $yearMonths[] = $spending;

        }
        usort($yearMonths, function ($a, $b) {
            return $a->happened_on < $b->happened_on;
        });

        return $this->responseObject($yearMonths);
    }
    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     */
    public function getMonthlyEarnings(Request $request){
        $user = Auth::guard('api')->user();
        $yearMonths = [];

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->earnings()->whereMonth('happened_on', '=',$request->input('month'))->whereYear('happened_on', '=',$request->input('year'))->get() as $earning) {
            $earning->type = 1;
            $yearMonths[] = $earning;
        }
        usort($yearMonths, function ($a, $b) {
            return $a->happened_on < $b->happened_on;
        });

        return $this->responseObject($yearMonths);
    }

    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     */
    public function getMonthlySpendings(Request $request){
        $user = Auth::guard('api')->user();
        $yearMonths = [];

        // Populate yearMonths with earnings
        foreach ($user->spaces()->first()->spendings()->whereMonth('happened_on', '=',$request->get('month'))->whereYear('happened_on', '=',$request->get('year'))->get() as $earning) {
            $earning->type = -1;
            $earning->tag = $earning->tag()->first();
            $yearMonths[] = $earning;
        }
        usort($yearMonths, function ($a, $b) {
            return $a->happened_on < $b->happened_on;
        });

        return $this->responseObject($yearMonths);
    }
    /**
     * @bodyParam year int required The specified year.
     * @bodyParam month int required The specified month.
     */
    public function getMonthlyCategories(Request $request){

        $month = $request->input('month');
        $year = $request->input('year');


        $user = Auth::guard('api')->user();
        $spaceId = $user->spaces()->first()->id;
        $tagRepository = new TagRepository();
        $mostExpensiveTags = $tagRepository->getMostExpensiveTags($spaceId, 5, $year, $month);
        return $this->responseObject($mostExpensiveTags);
    }

    public function getUserTags(Request $request){

        $user = Auth::guard('api')->user();
        $space = $user->spaces()->first();
        $userTags = $space->tags()->get();
        return $this->responseObject($userTags);
    }

    public function getUser(Request $request){
        $user = Auth::guard('api')->user();
        $user->overall_balance = $user->spaces()->first()->overallBalance();
        return $this->responseObject($user);
    }
    /**
     * @bodyParam avatar file required The photo for user.
     */
    public function setUserImage(Request $request){
        $user = Auth::guard('api')->user();
        $request->validate([
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif',
        ]);

        // Profile
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $fileName = $file->hashName();

            $image = Image::make($file)
                ->fit(500);

            Storage::put('public/avatars/' . $fileName, (string) $image->encode());

            $user->avatar = $fileName;
            $user->save();
            return $this->responseObject("Başarılı");
        }else{

        }

        return $this->failureResponse(Result::$FAILURE_PROCESS, "");

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