<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

    public function customer() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function item() {
        return $this->belongsTo(Item::class,'item_id');
    }
}
