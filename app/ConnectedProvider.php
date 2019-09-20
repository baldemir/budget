<?php

namespace App;

use App\Events\ProviderConnected;
use App\Events\ProviderDisconnected;
use App\Events\TagCreated;
use App\Events\TagDeleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConnectedProvider extends Model {
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'provider_id', 'access_token', 'expiry', 'refresh_token'];

    protected $dispatchesEvents = [
        'created' => ProviderConnected::class,
        'deleted' => ProviderDisconnected::class
    ];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function accounts() {
        return $this->provider()->first()->accounts($this->user()->first()->id);
    }



    public function provider() {
        return $this->belongsTo(Provider::class);
    }
}
