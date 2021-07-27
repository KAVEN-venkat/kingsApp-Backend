<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customerprice extends Model
{
    protected $table = 'customer_price';
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
