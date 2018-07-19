<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $collection = 'products';
    protected $fillable = ['discount_id'];

    public function discount(){
        return $this->belongsTo('App\Discount');
    }

    public function history(){
        return $this->hasMany('App\PriceHistory');
    }

}
