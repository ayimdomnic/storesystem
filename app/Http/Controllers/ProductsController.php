<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductEditRequest;
use App\Http\Traits\BrandAllTrait;
use App\Http\Traits\CategoryTrait;
use App\Http\Traits\SearchTrait;
use App\Http\Traits\CartTrait;
use Auth;
use Input;
use Illuminate\Http\Request;

use App\Http\Requests;

class ProductsController extends Controller
{
    


    public function showProducts()
    {
    	$product = Product::latest()->count();

    	$productCount = Product::all()->count();

    	$cart_count = $this->countProductsInCart();

    	return view('admin.product.show', compact('productCount','product', 'cart_count'));
    }

    public function addProduct()
    {
    	$categories = $this->parentCategory();
    	$brands = $this->brandsAll();
    	$cart_count = $this->countProductsInCart();

    	return view ('admin.products.add', compact('categories','brands','cart_count'));
    }

    public function addPostProduct(ProductRequest $request)
    {
    	$featured = Input::has('featured')? true: false;
    	$product_name = str_replace("/", "", $request->input('product_name'));

    	$product = Product::create([
    		'product_name' => $product_name,
    		'product_qty' => $request->input('product_qty'),
                'product_sku' => $request->input('product_sku'),
                'price' => $request->input('price'),
                'reduced_price' => $request->input('reduced_price'),
                'cat_id' => $request->input('cat_id'),
                'brand_id' => $request->input('brand_id'),
                'featured' => $featured,
                'description' => $request->input('description'),
                'product_spec' => $request->input('product_spec'),

    		]);

    	$product ->save();

    	flash()->sucess('Success','Product Created Successfully!');

    	return redirect()->route('admin.product.show');
    }
    public function categoryAPI()
    {
    	 // Get the "option" value from the drop-down.
        $input = Input::get('option');

        // Find the category name associated with the "option" parameter.
        $category = Category::find($input);

        // Find all the children (sub-categories) from the parent category
        // so we can display then in the sub-category drop-down list.
        $subcategory = $category->children();

        // Return a Response, and make a request to get the id and category (name)
        return \Response::make($subcategory->get(['id', 'category']));
    }

    public function editProduct($id)
    {
    	 // Find the product ID
        $product = Product::where('id', '=', $id)->find($id);
        // If no product exists with that particular ID, then redirect back to Show Products Page.
        if (!$product) {
            return redirect('admin/products');
        }
        // From Traits/CategoryTrait.php
        // ( This is to populate the parent category drop down in create product page )
        $categories = $this->parentCategory();
        // From Traits/BrandAll.php
        // Get all the Brands
        $brands = $this->BrandsAll();
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        // Return view with products and categories
        return view('admin.product.edit', compact('product', 'categories', 'brands', 'cart_count'));
    }

    public function updateProduct($id, ProductEditRequest $request)
    {
    	// Check if checkbox is checked or not for featured product
        $featured = Input::has('featured') ? true : false;
        // Find the Products ID from URL in route
        $product = Product::findOrFail($id);
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot edit Product because you are not an Administrator.');
        } else {
            // Update product
            $product->update(array(
                'product_name' => $request->input('product_name'),
                'product_qty' => $request->input('product_qty'),
                'product_sku' => $request->input('product_sku'),
                'price' => $request->input('price'),
                'reduced_price' => $request->input('reduced_price'),
                'cat_id' => $request->input('cat_id'),
                'brand_id' => $request->input('brand_id'),
                'featured' => $featured,
                'description' => $request->input('description'),
                'product_spec' => $request->input('product_spec'),
            ));
            // Update the product with all the validation rules
            $product->update($request->all());
            // Flash a success message
            flash()->success('Success', 'Product updated successfully!');
        }
        // Redirect back to Show all categories page.
        return redirect()->route('admin.product.show');
    
    }

    public function deleteProduct($id) {
        if (Auth::user()->id == 2) {
            // If user is a test user (id = 2),display message saying you cant delete if your a test user
            flash()->error('Error', 'Cannot delete Product because you are signed in as a test user.');
        } else {
            // Find the product id and delete it from DB.
            Product::findOrFail($id)->delete();
        }
        // Then redirect back.
        return redirect()->back();
    }

    public function displayImageUploadPage($id)
    {
    	// Get the product ID that matches the URL product ID.
        $product = Product::where('id', '=', $id)->get();
        // From Traits/CartTrait.php
        // ( Count how many items in Cart for signed in user )
        $cart_count = $this->countProductsInCart();
        return view('admin.product.upload', compact('product', 'cart_count'));
    }

    public function show($product_name)
    {
    	$product = Product::productLocatedAt($product_name);

    	$search = $this->search();
    	$category = $this->categoryAll();
    	$brands = $this->brandsAll();

    	$cart_count = $this->countProductsInCart();

        $similar_product = Product::where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('brand_id', '=', $product->brand_id)
                    ->orWhere('cat_id', '=', $product->cat_id);
            })->get();
        return view('pages.show_product', compact('product', 'search', 'brands', 'categories', 'similar_product', 'cart_count'));

    }
    //SPAGHETTI CODE MIGHT WANT TO FIX THIS LATER

}
