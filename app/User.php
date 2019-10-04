<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','facebook_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Accessors
    public function getAvatarAttribute($avatar) {
        return $avatar ? '/storage/avatars/' . $avatar : 'https://via.placeholder.com/250';
    }

    // Relations
    public function spaces() {
        return $this->belongsToMany(Space::class, 'user_space')->withPivot('role');
    }

    public function connectedProviders() {
        return $this->hasMany(ConnectedProvider::class);
    }


    public function addNew($input)
    {
        $check = static::where('facebook_id',$input['facebook_id'])->first();


        if(is_null($check)){
            return static::create($input);
        }


        return $check;
    }
}
