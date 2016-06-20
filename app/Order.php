<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //back to sublime LOL :)

    protected $table = 'orders';



    protected $fillable = [

    	'user_id',
    	'first_name',
        'last_name',
        'address',
        'address_2',
        'city',
        'state',
        'zip',
        'total',
        'full_name',

    ];

    public function orderItems() {
        return $this->belongsToMany('App\Product')->withPivot('qty', 'price', 'reduced_price', 'total', 'total_reduced');
    }
}
