<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function cities_state() {
        return $this->belongsTo(State::class,'state_id');
    }
    public function city_users() {
        return $this->hasMany(User::class,'state');
    }
}
