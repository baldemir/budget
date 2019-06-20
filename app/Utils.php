<?php

namespace App;


class Utils{
    public static function rand_color() {
        return str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
    public static function formatAmount($amount) {
        $number = number_format($amount/ 100, 2);
        $number =str_replace(',', '', $number);
        return $number;
    }

}
