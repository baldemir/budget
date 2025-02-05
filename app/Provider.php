<?php

namespace App;

use App\Events\TagCreated;
use App\Events\TagDeleted;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model {

    protected $fillable = ['name', 'alias', 'icon'];

}
