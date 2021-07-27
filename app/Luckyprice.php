<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Luckyprice extends Model
{
	public $table = "luckyprice";
    public function customer() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function item() {
        return $this->belongsTo(Item::class,'item_id');
    }
}
