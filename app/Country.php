<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function country_states() {
        return $this->hasMany(State::class,'country_id');
    }

    public function country_users() {
        return $this->hasMany(User::class,'country');
    }
}
