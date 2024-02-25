<?php

namespace App\Http\Controllers\API;

use App\Http\ApiController;
use App\Http\Requests\AddProductRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Services\Api\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    public function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_price' => 'numeric|nullable|min:0',
            'max_price' => 'numeric|nullable|min:0|gte:min_price',
            'category_id' => 'integer|nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $categoryId = $request->input('category_id');

        $productsQuery = Product::query();

        if ($minPrice !== null) {
            $productsQuery->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $productsQuery->where('price', '<=', $maxPrice);
        }
        if ($categoryId !== null) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->paginate(10);
        return ApiResponse::ok(
            __('Product Listing'),
            ProductResource::collection($products)
        );
    }

    public function addProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:products|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->validation_error_response($validator);
            }

            $input = $request->except('images');

            $product = Product::create($input);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/products', $imageName);

                    $imagePath = 'products/' . $imageName;

                    $imageModel = Image::create(['path' => $imagePath]);

                    $product->images()->attach($imageModel->id);
                }
            }

            return ApiResponse::ok(
                __('Product created successfully'),
                new ProductResource($product)
            );
        } catch (\Exception $e) {
            Log::error("Product adding failed: " . $e->getMessage());
            return ApiResponse::error('Something went wrong! Please try again.');
        }
    }

    public function productDetail(Request $request)
    {
        try {
            $id = $request->input('id');
            $product = Product::findOrFail($id);
            return ApiResponse::ok(
                __('Product created successfully'),
                new ProductResource($product)
            );
        } catch (\Exception $e) {
            Log::error("Product details failed: " . $e->getMessage());
            return ApiResponse::error('Failed to fetch details.');
        }
    }

    public function updateProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:products,name,' . $product->id . '|max:255',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required',
                'category_id' => 'required|exists:categories,id',
                'description' => 'required',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->validation_error_response($validator);
            }

            $input = $request->except('images');

            $product->update($input);

            if ($request->hasFile('images')) {
                $product->images()->delete();

                foreach ($request->file('images') as $image) {
                    $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/products', $imageName);

                    $imagePath = 'products/' . $imageName;

                    $imageModel = Image::create(['path' => $imagePath]);

                    $product->images()->attach($imageModel->id);
                }
            }

            return ApiResponse::ok(
                __('Product updated successfully'),
                new ProductResource($product)
            );
        } catch (\Exception $e) {
            Log::error("Product editing failed: " . $e->getMessage());
            return ApiResponse::error('No such product exists');
        }
    }

    public function deleteProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return ApiResponse::ok(
                __('Product deleted successfully'),
            );
        } catch (\Exception $e) {
            Log::error("Product deletion failed: " . $e->getMessage());
            return ApiResponse::error('Failed to delete product.');
        }
    }
    public function categories(Request $request)
    {
        $allCategories = Category::all();

        return ApiResponse::ok(
            __('Product Listing'),
            CategoryResource::collection($allCategories)
        );
    }
}
