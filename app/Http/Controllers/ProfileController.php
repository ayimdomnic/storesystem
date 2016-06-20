<?php

namespace App\Http\Controllers;
use App\User;
use App\Order;
use App\Http\Traits\BrandAllTrait;
use App\Http\Traits\CategoryTrait;
use App\Http\Traits\SearchTrait;
USE App\Http\Traits\CartTrait;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProfileController extends Controller
{
	


	public function __construct()
	{
		$this->middleware('auth');

		// parent::construct();
	}
    //


    public function index()
    {
    	$categories = $this->categoryAll();
    	$brands = $this->brandsAll();
    	$search = $this->search();

    	$cart_count = $this->countProductsInCart();

    	$username = \Auth::user();

    	$user_id = $username->id;

    	$orders = Order::where('user_id', '=', $user_id)->get();


    	return view('profile.index', compact('categories', 'brands', 'search', 'cart_count', 'username', 'orders'));
    }
}
