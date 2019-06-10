<?php


namespace App\Http\Controllers\API;


use App\Earning;
use App\Space;
use App\Spending;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;


class TransactionController extends BaseController
{

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'space_id' => 'required|exists:spaces,id',
            'tag_id' => 'nullable|exists:tags,id', // TODO CHECK IF TAG BELONGS TO USER
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/',
            'account_id' => 'nullable|exists:accounts,id', // TODO CHECK IF account BELONGS TO USER
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = Auth::guard('api')->user();

        $userOwnsSpace = Space::find($request->get('space_id'))->users()->get()->contains($user->id);

        if(!$userOwnsSpace){
            return $this->sendError('Authentication Error.');
        }





        $product = Spending::create($input);


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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Spending $product)
    {
        $product->delete();


        return $this->sendResponse($product->toArray(), 'Product deleted successfully.');
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
                        $tag = Tag::where('name', $elem[2])->first();
                        if($tag == null){

                            $tag = new Tag();
                            $tag->name = $elem[2];
                            $tag->color= 'FF0';
                            $tag->space_id = $spaceId;
                            $tag->save();
                        }
                        $isDuplicate = true;

                        $transaction->space_id = $spaceId;
                        if($amount <= 0){
                            $transaction->tag_id = $tag->id;
                            $transaction->account_id = 1;
                            $amount = $amount * -1;
                            $isDuplicate = Spending::where('description', $transaction->description)
                                ->where('happened_on', $transaction->happened_on)
                                ->where('space_id', $transaction->space_id)
                                ->where('tag_id', $transaction->tag_id)
                                ->where('amount', $amount)
                                ->first();

                        }else{
                            $isDuplicate = Earning::where('description', $transaction->description)
                                ->where('happened_on', $transaction->happened_on)
                                ->where('space_id', $transaction->space_id)
                                ->where('amount', $amount)
                                ->first();

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
                $tag = Tag::where('name', "-")->first();
                if($tag == null){

                    $tag = new Tag();
                    $tag->name = "-";
                    $tag->color= 'FF0';
                    $tag->space_id = $spaceId;
                    $tag->save();
                }
                $isDuplicate = true;

                $transaction->space_id = $spaceId;



                if($amount < 0){
                    $transaction->tag_id = $tag->id;
                    $transaction->account_id = 1;
                    $amount = $amount * -1;
                    $isDuplicate = Spending::where('description', $transaction->description)
                        ->where('happened_on', $transaction->happened_on)
                        ->where('space_id', $transaction->space_id)
                        ->where('tag_id', $transaction->tag_id)
                        ->where('amount', $amount)
                        ->first();

                }else{
                    $isDuplicate = Earning::where('description', $transaction->description)
                        ->where('happened_on', $transaction->happened_on)
                        ->where('space_id', $transaction->space_id)
                        ->where('amount', $amount)
                        ->first();

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

}