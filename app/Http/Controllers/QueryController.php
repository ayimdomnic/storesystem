<?php

namespace App\Http\Controllers;
use App\Brand;
use App\Product;
use App\Category;
use App\Http\Traits\BrandAllTrait;
use App\Http\Traits\CategoryTrait;
use App\Http\Traits\CartTrait;
use Illuminate\Http\Request;

use App\Http\Requests;

class QueryController extends Controller
{
    



    public function search()
    {
    	$categories = $this->categoryAll();

    	$brands = $this->brandsAll();
    	$cart_count = $this->countProductsInCart();

    	$query = Input::get('search');


    	$search = Product::where('product_name', 
    		'LIVE', '%'. $query . '%')->get();

    	if ($search->empty()) {
    		# code...
    		flash()->error('Error', 'No Search results for your query..');

    		return redirect('/');
    	}

    	return view ('pages.search', compact('search','query','categories','brands','cart_count'));
    }
}
