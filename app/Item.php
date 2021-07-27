<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function customPrice()
    {
        return $this->hasOne(customerPrice::class,'item_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class,'item_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
