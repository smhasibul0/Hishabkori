<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WareHouse;

class WareHouseController extends Controller
{
    public function AllWarehouse(){
        $warehouse = WareHouse::latest()->get();
        return view('admin.backend.warehouse.all_warehouse', compact('warehouse'));
    }
    // End Method

    public function AddWarehouse(){
        return view('admin.backend.warehouse.add_warehouse');
    }
    // End Method

    public function StoreWarehouse(Request $request){
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:ware_houses,email|max:255',
            'phone' => 'required|string|unique:ware_houses,phone|max:20',
            'address' => 'required|string|max:255',
        ]);

        WareHouse::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'phone' => $validateData['phone'],
            'address' => $validateData['address'],
        ]);

        $notification = array(
            'message' => 'Warehouse Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.warehouse')->with($notification); 
    }
    // End Method

    public function EditWarehouse($id){
        $warehouse = WareHouse::find($id);
        return view('admin.backend.warehouse.edit_warehouse', compact('warehouse'));
    }
    // End Method

    public function UpdateWarehouse(Request $request){
        $id = $request->id;

        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:ware_houses,email,' . $id . '|max:255',
            'phone' => 'required|string|unique:ware_houses,phone,' . $id . '|max:20',
            'address' => 'required|string|max:255',
        ]);

        WareHouse::find($id)->update([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'phone' => $validateData['phone'],
            'address' => $validateData['address'],
        ]);

        $notification = array(
            'message' => 'Warehouse Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.warehouse')->with($notification); 
    }
    // End Method

    public function DeleteWarehouse($id){
        WareHouse::find($id)->delete();

        $notification = array(
            'message' => 'Warehouse Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.warehouse')->with($notification); 
    }
    // End Method
}
