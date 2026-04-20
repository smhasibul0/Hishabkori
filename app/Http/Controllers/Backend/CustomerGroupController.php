<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;

class CustomerGroupController extends Controller
{
    public function AllCustomerGroup() {
        $customer_groups = CustomerGroup::latest()->get();
        return view('admin.backend.customer.all_customer_group', compact('customer_groups'));
    }

    public function StoreCustomerGroup(Request $request) {
        CustomerGroup::create([
            'name' => $request->customer_group_name,
        ]);

        $notification = array(
            'message'    => 'Customer Group Added Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer_group')->with($notification);
    }

    public function UpdateCustomerGroup(Request $request, $id) {
        CustomerGroup::findOrFail($id)->update([
            'name' => $request->customer_group_name,
        ]);

        $notification = array(
            'message'    => 'Customer Group Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer_group')->with($notification);
    }

    public function DeleteCustomerGroup($id) {
        CustomerGroup::findOrFail($id)->delete();

        $notification = array(
            'message'    => 'Customer Group Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer_group')->with($notification);

        
    }
}
