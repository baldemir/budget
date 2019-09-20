<?php

namespace App;

use App\Events\AccountCreated;
use App\Events\AccountDeleted;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['space_id', 'name'];

    protected $dispatchesEvents = [
        'created' => AccountCreated::class,
        'deleted' => AccountDeleted::class
    ];

    // Relations
    public function spendings() {
        return $this->hasMany(Spending::class);
    }


    public function provider() {
        return $this->belongsTo(Provider::class);
    }

    // Custom
    private static function randomColorPart() {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    public static function randomColor() {
        return self::randomColorPart() . self::randomColorPart() . self::randomColorPart();
    }
}
