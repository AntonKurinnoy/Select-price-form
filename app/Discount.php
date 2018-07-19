<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $collection = 'discounts';
    protected $fillable = ['date_start','date_end','duration'];

    public function products(){
        return $this->hasMany('App\Product');
    }
}
