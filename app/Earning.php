<?php

namespace App;

use App\Events\TransactionCreated;
use App\Events\TransactionDeleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Earning extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['happened_on', 'description', 'additional_desc',  'amount', 'space_id'];

    protected $dispatchesEvents = [
        'created' => TransactionCreated::class,
        'deleted' => TransactionDeleted::class
    ];

    // Accessors
    public function getFormattedAmountAttribute() {
        return number_format($this->amount / 100, 2);
    }

    public function getFormattedHappenedOnAttribute() {
        $secondsDifference = strtotime(date('Y-m-d')) - strtotime($this->happened_on);

        return ($secondsDifference / 60 / 60 / 24) . ' days ago';
    }

    // Relations
    public function tag() {
        return $this->belongsTo(Tag::class);
    }
}
