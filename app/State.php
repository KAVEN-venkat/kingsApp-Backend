<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public function states_country() {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state_cities() {
        return $this->hasMany(City::class,'state_id');
    }
    public function state_users() {
        return $this->hasMany(User::class,'state');
    }
}
