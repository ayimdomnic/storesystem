<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    
    protected $table = 'brands';


    protected $fillable = [
    	'brand_name',
    ];



    //relationships
    //i'm thinking one brand can have a bunch of products

    public function productBrand()
    {
    	return $this->hasMany('App\Product', 'brand_id');
    }
}
