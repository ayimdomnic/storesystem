<?php

namespace App\Http\Traits;


use App\Brand;

trait BrandAllTrait
{


	public function brandsAll()
	{
		return Brand::all();
	}
}