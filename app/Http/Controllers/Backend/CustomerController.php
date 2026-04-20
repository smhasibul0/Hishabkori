<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;

class CustomerController extends Controller
{
    public function AllCustomer() {
        $customers = Customer::latest()->get();
        return view('admin.backend.customer.all_customer', compact('customers'));
    }

    public function AddCustomer() {
        $customerGroups = CustomerGroup::latest()->get();
        return view('admin.backend.customer.add_customer', compact('customerGroups'));
    }

    public function StoreCustomer(Request $request) {

        if ($request->supplier_type === 'business') {
            $customer = Customer::create([
                'business_name'    => $request->business_name,
                'prefix'           => null,
                'first_name'       => null,
                'middle_name'      => null,
                'last_name'        => null,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'tax_number'       => $request->tax_number,
                'credit_limit'     => $request->credit_limit,
                'pay_term'         => $request->pay_term,
                'openning_balance' => $request->openning_balance ?? 0,
                'advance_balance'  => 0,
                'customer_group'   => $request->customer_group,
                'address'          => $request->address,
                'sale_due'         => 0,
                'sale_return_due'  => 0,
            ]);
        } else {
            $customer = Customer::create([
                'business_name'    => null,
                'prefix'           => $request->prefix,
                'first_name'       => $request->first_name,
                'middle_name'      => $request->middle_name,
                'last_name'        => $request->last_name,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'tax_number'       => $request->tax_number,
                'credit_limit'     => $request->credit_limit,
                'pay_term'         => $request->pay_term,
                'openning_balance' => $request->openning_balance ?? 0,
                'advance_balance'  => 0,
                'customer_group'   => $request->customer_group,
                'address'          => $request->address,
                'sale_due'         => 0,
                'sale_return_due'  => 0,
            ]);
        }

        // Generate Customer ID
        $customer->contact_id = 'CUS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
        $customer->save();

        $notification = array(
            'message'    => 'Customer Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }

    public function EditCustomer($id) {
        $customer       = Customer::findOrFail($id);
        $customerGroups = CustomerGroup::latest()->get();
        return view('admin.backend.customer.edit_customer', compact('customer', 'customerGroups'));
    }

    public function UpdateCustomer(Request $request, $id) {
        $customer = Customer::findOrFail($id);

        if ($request->name_type === 'business_name') {
            $customer->business_name = $request->business_name;
            $customer->prefix        = null;
            $customer->first_name    = null;
            $customer->middle_name   = null;
            $customer->last_name     = null;
        } else {
            $customer->business_name = null;
            $customer->prefix        = $request->prefix;
            $customer->first_name    = $request->first_name;
            $customer->middle_name   = $request->middle_name;
            $customer->last_name     = $request->last_name;
        }

        $customer->email            = $request->email;
        $customer->phone            = $request->phone;
        $customer->tax_number       = $request->tax_number;
        $customer->credit_limit     = $request->credit_limit;
        $customer->pay_term         = $request->pay_term;
        $customer->openning_balance = $request->openning_balance;
        $customer->customer_group   = $request->customer_group;
        $customer->address          = $request->address;

        $customer->save();

        $notification = array(
            'message'    => 'Customer Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }

    public function DeleteCustomer($id) {
        Customer::findOrFail($id)->delete();

        $notification = array(
            'message'    => 'Customer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.customer')->with($notification);
    }
}