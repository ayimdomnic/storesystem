<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Http\Traits\CartTrait;
use App\Http\Traits\BrandAllTrait;
use App\Http\Traits\CategoryTrait;
use App\Requests\CategoryRequest;

use App\Http\Requests;

class CategoriesController extends Controller
{
    


    public function showCategories()
    {
    	$categories = $this->categoryAll();
    	 $cart_count = $this->countProductsInCart();

        return view('admin.category.show', compact('cart_count'))->with('categories', $categories);
    }

    public function getProductsForSubCategory($id) {
        // Get the Category name under this category
        $categories = Category::where('id', '=', $id)->get();
        // Get all products under this sub-category
        $products = Product::where('cat_id', '=', $id)->get();
        // Count to see if there are any products under this category
        $count = Product::where('cat_id', '=', $id)->count();
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.category.show_products', compact('categories', 'products', 'count', 'cart_count'));
    }

    public function addCategories() {
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.category.add', compact('cart_count'));
    }

    public function addPostCategories(CategoryRequest $request) {
        // Assign $category to the Category Model, and request all validation rules
        $category = new Category($request->all());
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot create Category because you are signed in as a test user.');
        } else {
            // Then save the newly created category in DB
            $category->save();
            // Flash a success message
            flash()->success('Success', 'Category added successfully!');
        }
        // Redirect back to Show all categories page.
        return redirect()->route('admin.category.show');
    }

    public function editCategories($id) {
        // Select all from categories where the id = the id on the page
        $category = Category::where('id', '=', $id)->find($id);
        // If no category exists with that particular ID, then redirect back to Show Category Page.
        if (!$category) {
            return redirect('admin/categories');
        }
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.category.edit', compact('category', 'cart_count'));
    }

    public function updateCategories($id, CategoryRequest $request) {
        // Find the category id being updated
        $category = Category::findOrFail($id);
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot edit Category because you are signed in as a test user.');
        } else {
            // Update the category with all the validation rules from CategoryRequest.php
            $category->update($request->all());
            // Flash a success message
            flash()->success('Success', 'Category updated successfully!');
        }
        // Redirect back to Show all categories page.
        return redirect()->route('admin.category.show');
    }

    public function deleteCategories($id) {
        // Find the category id and delete it from DB.
        $delete = Category::findOrFail($id);
        // Get all sub categories where the parent_id = to the category id
        $sub_category = Category::where('parent_id', '=', $id)->count();
        // If there are any sub-categories under a parent category, then throw
        // a error overlay message, saying to delete all sub categories under the parent
        // category, else delete the parent category
        if ($sub_category > 0) {
            // Flash a error overlay message
            flash()->customErrorOverlay('Error', 'There are sub-categories under this parent category. Cannot delete this category until all sub-categories under this parent category are deleted');
        } elseif (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot delete Category because you are signed in as a test user.');
        } else {
            $delete->delete();
        }
        // Then redirect back.
        return redirect()->back();
    }

    public function addSubCategories($id) {
        $category = Category::findOrFail($id);
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.category.addsub', compact('category', 'cart_count'));
    }

     public function addPostSubCategories($id, CategoryRequest $request) {
        // Find the Parent Category ID
        $category = Category::findOrFail($id);
        // Create the new Subcategory
        $subcategory = new Category($request->all());
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot create Sub-Category because you are signed in as a test user.');
        } else {
            // Save the new subcategory into the relationship
            $category->children()->save($subcategory);
            // Flash a success message
            flash()->success('Success', 'Sub-Category added successfully!');
        }
        // Redirect back to Show all categories page.
        return redirect()->route('admin.category.show');
    }

    public function editSubCategories($id) {
        // Select all from categories where the id = the id on the page
        $category = Category::where('id', '=', $id)->find($id);
        // If no sub-category exists with that particular ID, then redirect back to Show Category Page.
        if (!$category) {
            return redirect('admin/categories');
        }
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.category.editsub', compact('category', 'cart_count'));
    }


    public function updateSubCategories($id, CategoryRequest $request) {
        // Find the category id being updated
        $category = Category::findOrFail($id);
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot edit Sub-Category because you are signed in as a test user.');
        } else {
            // Update the category with all the validation rules from CategoryRequest.php
            $category->update($request->all());
            // Flash a success message
            flash()->success('Success', 'Sub-Category updated successfully!');
        }
        // Redirect back to Show all categories page.
        return redirect()->route('admin.category.show');
    }

    public function deleteSubCategories($id) {
        // Find the sub-category id and delete it from DB.
        $delete_sub = Category::findOrFail($id);
        // Get all sub categories where the parent_id = to the category id
        $products = Product::where('cat_id', '=', $id)->count();
        // If there are any sub-categories under a parent category, then throw
        // a error overlay message, saying to delete all sub categories under the parent
        // category, else delete the parent category
        if ($products > 0) {
            // Flash a error overlay message
            flash()->customErrorOverlay('Error', 'There are products under this sub-category. Cannot delete this sub-category until all products under this sub-category are deleted');
        } elseif (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot delete Sub-Category because you are signed in as a test user.');
        } else {
            $delete_sub->delete();
        }
        // Then redirect back.
        return redirect()->back();
    }


}
