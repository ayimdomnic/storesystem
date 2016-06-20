<?php 

namespace App\Product;

use Illuminate\Support\Facades\Input;

trait SearchTrait
{
	public function search()
	{
		$query = Input::get('search');

		return Product::where('product_name', 'LIKE', '%'. $query . '%')->paginate(200);
	}
}