<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Warehouse;

class ProductController extends Controller
{
    public function AllCategory(){
        $category = ProductCategory::with('children')->whereNull('parent_id')->latest()->get();
        return view('admin.backend.category.all_category', compact('category'));
    }

    public function StoreCategory(Request $request){
        ProductCategory::insert([
            'category_name' => $request->category_name,
            'category_code' => strtolower(str_replace(' ', '-', $request->category_name)),
            'description'   => $request->description,
            'parent_id'     => $request->is_sub ? $request->parent_id : null,
        ]);

        $notification = array(
            'message'    => 'ProductCategory Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function EditCategory($id){
        $category = ProductCategory::find($id);
        return response()->json($category);
    }

    public function UpdateCategory(Request $request){
        $cat_id = $request->cat_id;
        ProductCategory::find($cat_id)->update([
            'category_name' => $request->category_name,
            'category_code' => strtolower(str_replace(' ', '-', $request->category_name)),
            'description'   => $request->description,
            'parent_id'     => $request->is_sub ? $request->parent_id : null,
        ]);

        $notification = array(
            'message'    => 'ProductCategory Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    public function DeleteCategory($id){
        ProductCategory::find($id)->delete();
        $notification = array(
            'message'    => 'ProductCategory Delete Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    // ==================== PRODUCT METHODS ====================

    public function AllProduct(){
        $products = Product::with(['category', 'subcategory', 'brand', 'supplier', 'warehouse'])->latest()->get();
        return view('admin.backend.product.all_product', compact('products'));
    }

    public function AddProduct(){
        $categories = ProductCategory::whereNull('parent_id')->latest()->get();
        $brands     = Brand::latest()->get();
        $suppliers  = Supplier::latest()->get();
        $warehouses = Warehouse::latest()->get();
        return view('admin.backend.product.add_product', compact('categories', 'brands', 'suppliers', 'warehouses'));
    }

    public function StoreProduct(Request $request){

        // Handle images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $imageName);
                $imagePaths[] = 'uploads/products/' . $imageName;
            }
        }

        $product = Product::create([
            'product_name'   => $request->product_name,
            'product_code'   => $request->product_code ?: null, // auto-generate after insert
            'image'          => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id ?: null,
            'brand_id'       => $request->brand_id,
            'supplier_id'    => $request->supplier_id,
            'warehouse_id'   => $request->warehouse_id,
            'description'    => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'quantity'       => $request->manage_stock ? ($request->quantity ?? 0) : 0,
            'alert_quantity' => $request->alert_quantity ?? 0,
            'manage_stock'   => $request->has('manage_stock') ? 1 : 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        // Auto-generate product code if left empty
        if (!$request->product_code) {
            $product->product_code = 'PRD' . str_pad($product->id, 5, '0', STR_PAD_LEFT);
            $product->save();
        }

        $notification = array(
            'message'    => 'Product Added Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.product')->with($notification);
    }

    public function EditProduct($id){
        $product    = Product::findOrFail($id);
        $categories = ProductCategory::whereNull('parent_id')->latest()->get();
        $subcategories = ProductCategory::where('parent_id', $product->category_id)->get();
        $brands     = Brand::latest()->get();
        $suppliers  = Supplier::latest()->get();
        $warehouses = Warehouse::latest()->get();
        return view('admin.backend.product.edit_product', compact('product', 'categories', 'subcategories', 'brands', 'suppliers', 'warehouses'));
    }

    public function UpdateProduct(Request $request, $id){
        $product = Product::findOrFail($id);

        // Handle new images
        $imagePaths = json_decode($product->image, true) ?? [];

        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($imagePaths as $oldImage) {
                if (file_exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $imageName);
                $imagePaths[] = 'uploads/products/' . $imageName;
            }
        }

        $product->update([
            'product_name'   => $request->product_name,
            'product_code'   => $request->product_code ?: $product->product_code,
            'image'          => !empty($imagePaths) ? json_encode($imagePaths) : $product->image,
            'category_id'    => $request->category_id,
            'subcategory_id' => $request->subcategory_id ?: null,
            'brand_id'       => $request->brand_id,
            'supplier_id'    => $request->supplier_id,
            'warehouse_id'   => $request->warehouse_id,
            'description'    => $request->description,
            'purchase_price' => $request->purchase_price,
            'selling_price'  => $request->selling_price,
            'quantity'       => $request->manage_stock ? ($request->quantity ?? 0) : 0,
            'alert_quantity' => $request->alert_quantity ?? 0,
            'manage_stock'   => $request->has('manage_stock') ? 1 : 0,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        $notification = array(
            'message'    => 'Product Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.product')->with($notification);
    }

    public function DeleteProduct($id){
        $product = Product::findOrFail($id);

        // Delete images from storage
        $images = json_decode($product->image, true) ?? [];
        foreach ($images as $image) {
            if (file_exists(public_path($image))) {
                unlink(public_path($image));
            }
        }

        $product->delete();

        $notification = array(
            'message'    => 'Product Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.product')->with($notification);
    }

    // AJAX: get subcategories by category
    public function GetSubcategories($categoryId){
        $subcategories = ProductCategory::where('parent_id', $categoryId)->get();
        return response()->json($subcategories);
    }
}