<?php
/**
 * Created by PhpStorm.
 * User: ulakbim
 * Date: 18.07.2019
 * Time: 16:33
 */

namespace App;




class Result
{
    public static $SUCCESS;
    public static $SUCCESS_EMPTY;
    public static $SUCCESS_PARTIAL;
    public static $FAILURE_PROCESS;
    public static $FAILURE_TCKIMLIK;
    public static $FAILURE_CAPTCHA;
    public static $FAILURE_DB;
    public static $FAILURE_DB_DUPLICATE_EMAIL;
    public static $FAILURE_DB_DUPLICATE_TCKN;
    public static $FAILURE_DB_DUPLICATE_FORGOT_PASSWORD;
    public static $FAILURE_PARAM_MISMATCH;
    public static $FAILURE_AUTH_WRONG;
    public static $FAILURE_AUTH_UNCONFIRMED;
    public static $FAILURE_TOKEN_ERROR;
    public static $FAILURE_PRODUCT_LIMIT_EXCEEDED;
    public $resultText;
    public $resultCode;
    public $content;



    public static function constructor_default(){
        $result = new Result();
        $result->success= true;
        $result->resultCode = "GUPPY.001";
        $result->resultText = "Process is successfull.";
        self::$SUCCESS = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.101";
        $result->resultText = "Process is successfull but no results.";
        self::$SUCCESS_EMPTY = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.002";
        $result->resultText = "Process is partially successfull.";
        self::$SUCCESS_PARTIAL = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.400";
        $result->resultText = "Process error occured.";
        self::$FAILURE_PROCESS = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.500";
        $result->resultText = "DB error occured.";
        self::$FAILURE_DB = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.501";
        $result->resultText = "Duplicate Email.";
        self::$FAILURE_DB_DUPLICATE_EMAIL = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.502";
        $result->resultText = "Duplicate TCKN.";
        self::$FAILURE_DB_DUPLICATE_TCKN = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.503";
        $result->resultText = "Duplicate forgotten password.";
        self::$FAILURE_DB_DUPLICATE_FORGOT_PASSWORD = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.301";
        $result->resultText = "Captcha verification failed.";
        self::$FAILURE_CAPTCHA = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.401";
        $result->resultText = "TC Kimlik numarası bilgilerle eşleşmiyor.";
        self::$FAILURE_TCKIMLIK = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.601";
        $result->resultText = "Parametreler eşleşmiyor.";
        self::$FAILURE_PARAM_MISMATCH = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.701";
        $result->resultText = "Authentication error.";
        self::$FAILURE_AUTH_WRONG = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.702";
        $result->resultText = "User not confirmed.";
        self::$FAILURE_AUTH_UNCONFIRMED = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.703";
        $result->resultText = "Token error.";
        self::$FAILURE_TOKEN_ERROR = $result;

        $result = new Result();
        $result->resultCode = "GUPPY.801";
        $result->resultText = "Hesabınız için belirlenen ürün limiti yetersizdir.";
        self::$FAILURE_PRODUCT_LIMIT_EXCEEDED = $result;

    }
    public function setContent($obj){
        $this->content = $obj;
        return $this;
    }
    public function __toString()
    {
        return json_encode($this);
    }
}
Result::constructor_default();