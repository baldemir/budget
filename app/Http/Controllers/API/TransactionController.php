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


        return $this->sendResponse($products->toArray(), 'Products retrieved successfully.');
    }

}