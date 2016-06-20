<?php

namespace App\Http\Traits;

use App\Category;

trait CategoryTrait
{

	public function categoryAll()
	{
		return Category::whereNull('parent_id')->with('children')->get();
	}


	public function parentCategory()
	{
		return Category::whereNull('parent_id')->get();
	}
}