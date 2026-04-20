<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BrandController extends Controller
{
    public function AllBrand(){
        $brand = Brand::latest()->get();
        return view('admin.backend.brand.all_brand', compact('brand'));
    }
    // End Method

    public function AddBrand(){
        return view('admin.backend.brand.add_brand');
    }
    // End Method

    public function StoreBrand(Request $request){
        if ($request->file('image')) {

            $image = $request->file('image');

            // Create ImageManager (v3)
            $manager = new ImageManager(new Driver());

            // Generate unique name
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            // Read image instead of make()
            $img = $manager->read($image);

            // Resize
            $img = $img->resize(100, 90);

            // Save image
            $img->save(public_path('upload/brand/' . $name_gen));

            // Store path in DB
            $save_url = 'upload/brand/' . $name_gen;

            // Step 1: Create Brand first
            $brand = Brand::create([
                'brand_name'  => $request->brand_name,
                'brand_image' => $save_url,
            ]);

            // Step 2: Generate Brand ID like BR0001
            $brand->brand_id = 'BR' . str_pad($brand->id, 4, '0', STR_PAD_LEFT);
            $brand->save();
        }
        $notification = array(
            'message' => 'Brand Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.brand')->with($notification); 
    }
    // End Method

    public function EditBrand($brand_id){
        $brand = Brand::where('brand_id', $brand_id)->first();
        return view('admin.backend.brand.edit_brand', compact('brand'));
    }
    // End Method

    public function UpdateBrand(Request $request)
    {
        $brand = Brand::where('brand_id', $request->brand_id)->firstOrFail();

        if ($request->file('brand_image')) {

            $image = $request->file('brand_image');

            $manager = new ImageManager(new Driver());

            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            $img = $manager->read($image)->resize(100, 90);

            $img->save(public_path('upload/brand/' . $name_gen));

            // Delete old image if exists
            if ($brand->image && file_exists(public_path($brand->image))) {
                @unlink(public_path($brand->image));
            }

            $brand->update([
                'brand_name'  => $request->brand_name,
                'brand_image' => 'upload/brand/' . $name_gen,
            ]);

        } else {

            $brand->update([
                'brand_name'  => $request->brand_name,
            ]);
        }

        $notification = array(
            'message' => 'Brand Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.brand')->with($notification);
    }
    // End Method

    public function DeleteBrand($brand_id)
    {
        $brand = Brand::where('brand_id', $brand_id)->firstOrFail();

        // Delete image if exists
        if ($brand->image && file_exists(public_path($brand->image))) {
            @unlink(public_path($brand->image));
        }

        $brand->delete();

        $notification = array(
            'message' => 'Brand Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.brand')->with($notification);
    }
}