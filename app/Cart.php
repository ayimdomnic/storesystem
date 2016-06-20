<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table ='cats';



    protected $fillable = [
    	'user_id', 'product_id','qty','total',
    ];

    //product
    public function products()
    {
    	return $this->belongsTo('App\Product','product_id');
    }

    //user
    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }
}
