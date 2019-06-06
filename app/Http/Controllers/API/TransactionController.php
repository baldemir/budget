<?php


namespace App\Http\Controllers\API;


use App\Spending;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
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


        return $this->sendResponse($products->toArray(), 'Transactions retrieved successfully.');
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
            'space_id' => 'required',
            'tag_id' => 'required'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        try{
            $product = Spending::create($input);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }



        return $this->sendResponse($product->toArray(), 'Product created successfully.');
    }

}