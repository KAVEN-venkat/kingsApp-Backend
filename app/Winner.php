<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
	public $table = "winner";
	
    public function customer() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function item() {
        return $this->belongsTo(Item::class,'item_id');
    }
}
