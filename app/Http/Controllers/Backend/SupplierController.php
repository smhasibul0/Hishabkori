<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function AllSupplier() {
        $suppliers = Supplier::latest()->get();
        return view('admin.backend.supplier.all_supplier', compact('suppliers'));
    }

    public function AddSupplier() {
        return view('admin.backend.supplier.add_supplier');
    }

    public function StoreSupplier(Request $request) {

        $supplier = Supplier::create([
            'name' => $request->name,
            'business_name' => $request->business_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ]);

        // Generate Supplier ID
        $supplier->contact_id = 'SUP' . str_pad($supplier->id, 4, '0', STR_PAD_LEFT);
        $supplier->save();

        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }

    public function EditSupplier($id) {
        $supplier = Supplier::findOrFail($id);
        return view('admin.backend.supplier.edit_supplier', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id) {
        $supplier = Supplier::findOrFail($id);

        // Save the value to the correct column based on name_type
        if ($request->name_type === 'business_name') {
            $supplier->business_name = $request->supplier_name_value;
            $supplier->name = null; // keep the other column empty
        } else {
            $supplier->name = $request->supplier_name_value;
            $supplier->business_name = null; // keep the other column empty
        }

        $supplier->email   = $request->email;
        $supplier->phone   = $request->phone;
        $supplier->address = $request->address;

        $supplier->save();

        return redirect()->back()->with('success', 'Supplier updated successfully!');
    }

    public function DeleteSupplier($id) {
        Supplier::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.supplier')->with($notification);
    }
}
