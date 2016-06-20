<?php

namespace App\Http\Controllers;

use App\Cart;
use App\User;
use App\Order;
use App\Product;
use App\Http\Traits\CartTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;

use App\Http\Requests;

class AdminController extends Controller
{
    


    public function index()
    {
    	$cart_count = $this->countProductsInCart();

    	$orders = Order::all();
    	$count_total= Order::sum('total');
    	$users = User::all();
    	$carts = Cart::all();
    	$products = Product::all();

    	$product_quantity = Product::where('product_qty', '=', 0)->get();
    	return view('admin.pages.index', compact('cart_count','orders','users','carts','count_total','products','product_quantity'));
    }

    public function delete($id)
    {
    	$user = User::findOrFail($id);

    	if (Auth::user()->id == 2) {
    		# code...
    		flash()->error('error','You need administration permissions to delete Users');
    	}elseif ($user->admin == 1) {
    		# code...
    		flash()->error('error', 'cannot delete Admin idiot SMH!');

    	}else{
    		$user->delete();
    	}

    	return redirect()->back();
    }

    public function deleteCart($id)
    {
    	$cart = Cart::findOrFail($id);

    	if (Auth::user()->id == 2) {
    		# code...
    		flash()->error('error', 'You are Just a user why you trying to delete this cat?');
    	}else {
    		$cart->delete();
    	}
    }

    public function update(Request $request)
    {
    	$this->validate($request,[
    			'product_qty' => 'required|max:2|min1',
    		]);
    	$qty = Input::get('product_qty');
    	$product_id = Input::get('product');

    	$product = Product::find($product_id);

    	$product_qty = Product::where('id', '=', $product_id);

    	if (Auth::user()->id == 2){

    		flash()->error('Error', 'Cannot Update Quantity because you are not an admin son');
    	}else{
    		$product_qty->update(array(
    			'product_qty' => $qty
    			));
    	}

    	return redirect()->back();
    }
}
