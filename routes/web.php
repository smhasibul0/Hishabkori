<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\CustomerGroupController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\PaymentAccountController;
use App\Http\Controllers\Backend\AccountTypeController;
use App\Http\Controllers\Backend\PurchaseController;

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');
    
    Route::post('/profile/store', [AdminController::class, 'ProfileStore'])->name('profile.store');

    Route::post('/admin/password/update', [AdminController::class, 'AdminPasswordUpdate'])->name('admin.password.update');
});

Route::middleware('auth')->group(function () {
    Route::controller(BrandController::class)->group(function () {
        Route::get('/all/brand', 'AllBrand')->name('all.brand');
        Route::get('/add/brand', 'AddBrand')->name('add.brand');
        Route::post('/store/brand', 'StoreBrand')->name('store.brand');
        Route::get('/edit/brand/{brand_id}', 'EditBrand')->name('edit.brand');
        Route::post('/update/brand', 'UpdateBrand')->name('update.brand');
        Route::get('/delete/brand/{brand_id}', 'DeleteBrand')->name('delete.brand');
    });

    Route::controller(WarehouseController::class)->group(function () {
        Route::get('/all/warehouse', 'AllWarehouse')->name('all.warehouse');
        Route::get('/add/warehouse', 'AddWarehouse')->name('add.warehouse');
        Route::post('/store/warehouse', 'StoreWarehouse')->name('store.warehouse');
        Route::get('/edit/warehouse/{id}', 'EditWarehouse')->name('edit.warehouse');
        Route::post('/update/warehouse', 'UpdateWarehouse')->name('update.warehouse');
        Route::get('/delete/warehouse/{id}', 'DeleteWarehouse')->name('delete.warehouse');
    });

    route::controller(SupplierController::class)->group(function () {
        Route::get('/all/supplier', 'AllSupplier')->name('all.supplier');
        Route::get('/add/supplier', 'AddSupplier')->name('add.supplier');
        Route::post('/store/supplier', 'StoreSupplier')->name('store.supplier');
        Route::get('/edit/supplier/{id}', 'EditSupplier')->name('edit.supplier');
        Route::post('/update/supplier', 'UpdateSupplier')->name('update.supplier');
        Route::get('/delete/supplier/{id}', 'DeleteSupplier')->name('delete.supplier');
    });

    route::controller(CustomerController::class)->group(function () {  
        Route::get('/all/customer', 'AllCustomer')->name('all.customer');
        Route::get('/add/customer', 'AddCustomer')->name('add.customer');
        Route::post('/store/customer', 'StoreCustomer')->name('store.customer');
        Route::get('/edit/customer/{id}', 'EditCustomer')->name('edit.customer');
        Route::post('/update/customer/{id}', 'UpdateCustomer')->name('update.customer');
        Route::get('/delete/customer/{id}', 'DeleteCustomer')->name('delete.customer');
    });

    Route::controller(CustomerGroupController::class)->group(function () {
        Route::get('/all/customer-group', 'AllCustomerGroup')->name('all.customer_group');
        Route::post('/store/customer-group', 'StoreCustomerGroup')->name('store.customer_group');
        Route::post('/update/customer-group/{id}', 'UpdateCustomerGroup')->name('update.customer_group');
        Route::get('/delete/customer-group/{id}', 'DeleteCustomerGroup')->name('delete.customer_group');
    });
    Route::controller(ProductController::class)->group(function(){
        Route::get('/all/category', 'AllCategory')->name('all.category');
        Route::post('/store/category', 'StoreCategory')->name('store.category'); 
        Route::get('/edit/category/{id}', 'EditCategory');
        Route::post('/update/category', 'UpdateCategory')->name('update.category'); 
        Route::get('/delete/category/{id}', 'DeleteCategory')->name('delete.category');
    });
    // Product Routes
    Route::controller(ProductController::class)->group(function () {
        Route::get('/all/product', 'AllProduct')->name('all.product');
        Route::get('/add/product', 'AddProduct')->name('add.product');
        Route::post('/store/product', 'StoreProduct')->name('store.product');
        Route::get('/edit/product/{id}', 'EditProduct')->name('edit.product');
        Route::post('/update/product/{id}', 'UpdateProduct')->name('update.product');
        Route::get('/delete/product/{id}', 'DeleteProduct')->name('delete.product');
        Route::get('/get/subcategories/{categoryId}', 'GetSubcategories')->name('get.subcategories');
    });
    Route::controller(PaymentAccountController::class)->group(function () {
        // Payment Accounts
        Route::get('/payment-accounts',               [PaymentAccountController::class, 'index'])->name('payment.accounts');
        Route::post('/payment-accounts',              [PaymentAccountController::class, 'store'])->name('payment.account.store');
        Route::get('/payment-accounts/{id}/edit',     [PaymentAccountController::class, 'edit'])->name('payment.account.edit');
        Route::put('/payment-accounts/{id}',          [PaymentAccountController::class, 'update'])->name('payment.account.update');
        Route::patch('/payment-accounts/{id}/close',  [PaymentAccountController::class, 'close'])->name('payment.account.close');
        Route::get('/payment-accounts/{id}/book',     [PaymentAccountController::class, 'book'])->name('payment.account.book');
        Route::post('/payment-accounts/fund-transfer',[PaymentAccountController::class, 'fundTransfer'])->name('payment.account.fund.transfer');
        Route::post('/payment-accounts/deposit',      [PaymentAccountController::class, 'deposit'])->name('payment.account.deposit');
        
        // Account Types
        Route::post('/account-types',         [AccountTypeController::class, 'store'])->name('account.type.store');
        Route::get('/account-types/{id}/edit',[AccountTypeController::class, 'edit'])->name('account.type.edit');
        Route::put('/account-types/{id}',     [AccountTypeController::class, 'update'])->name('account.type.update');
        Route::delete('/account-types/{id}',  [AccountTypeController::class, 'destroy'])->name('account.type.delete');
    });
    Route::controller(PurchaseController::class)->group(function () {
        // Pages
        Route::get('/purchase/all',    [PurchaseController::class, 'allPurchase'])->name('all.purchase');
        Route::get('/purchase/add',    [PurchaseController::class, 'addPurchase'])->name('add.purchase');
        Route::post('/purchase/store', [PurchaseController::class, 'storePurchase'])->name('store.purchase');
        
        // ⭐ AJAX routes MUST come BEFORE the {id} route
        Route::get('/purchase/search-products',  [PurchaseController::class, 'searchProducts'])->name('purchase.search.products');
        Route::get('/purchase/payment-accounts', [PurchaseController::class, 'getPaymentAccounts'])->name('purchase.payment.accounts');
        
        // ⭐ Generic routes with parameters come LAST
        Route::get('/purchase/{id}',   [PurchaseController::class, 'viewPurchase'])->name('view.purchase');
        Route::get('/purchase/delete/{id}', [PurchaseController::class, 'deletePurchase'])->name('delete.purchase');
        Route::get('/purchase/{id}/show',             [PurchaseController::class, 'showPurchase'])->name('purchases.show');
        Route::get('/purchase/{id}/print',            [PurchaseController::class, 'printPurchase'])->name('purchases.print');
        Route::get('/purchase/{id}/download-document',[PurchaseController::class, 'downloadDocument'])->name('purchases.download-document');
        Route::get('/purchase/{id}/view-document',    [PurchaseController::class, 'viewDocument'])->name('purchases.view-document');
        Route::post('/purchase/{id}/add-payment',     [PurchaseController::class, 'addPayment'])->name('purchases.add-payment');
        Route::get('/purchase/{id}/view-payments',    [PurchaseController::class, 'viewPayments'])->name('purchases.view-payments');
        Route::get('/purchase/edit/{id}', [PurchaseController::class, 'editPurchase'])->name('edit.purchase');
    });
});