@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Add Product</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('all.product') }}">Products</a></li>
                    <li class="breadcrumb-item active">Add Product</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Product</h5>
                    </div>
                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.product') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                            @csrf

                            {{-- Product Name --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="product_name" placeholder="Enter product name">
                            </div>

                            {{-- Product Code --}}
                            <div class="col-md-6">
                                <label class="form-label">Product Code <span class="text-muted" style="font-size:.75rem;">(leave empty to auto-generate)</span></label>
                                <input type="text" class="form-control" name="product_code" placeholder="Auto-generate if empty">
                            </div>

                            {{-- Category --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_id" id="categorySelect">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Subcategory --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Subcategory</label>
                                <select class="form-control" name="subcategory_id" id="subcategorySelect">
                                    <option value="">-- Select Subcategory --</option>
                                </select>
                            </div>

                            {{-- Brand --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="form-control" name="brand_id">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Supplier --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-control" name="supplier_id">
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">
                                            {{ $supplier->business_name ?: $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Warehouse --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select class="form-control" name="warehouse_id">
                                    <option value="">-- Select Warehouse --</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Manage Stock --}}
                            <div class="col-md-6 d-flex align-items-center gap-3 mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStockToggle" value="1">
                                    <label class="form-check-label fw-semibold" for="manageStockToggle">Manage Stock</label>
                                </div>
                            </div>

                            {{-- Alert Quantity (shown only when manage stock is ON) --}}
                            <div class="col-md-12 d-none" id="stockFields">
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Alert Quantity <span class="text-muted" style="font-size:.75rem;">(alert when stock falls to or below this)</span></label>
                                        <input type="number" class="form-control" name="alert_quantity" value="0" min="0">
                                    </div>
                                </div>
                            </div>


                            

                            
                            {{-- Status --}}
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveToggle" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="isActiveToggle">Active</label>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Enter product description"></textarea>
                            </div>

                            {{-- Images --}}
                            <div class="col-md-12">
                                <label class="form-label">Product Images</label>
                                <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                <small class="text-muted">You can select multiple images</small>
                            </div>

                            {{-- Purchase Price --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="purchase_price" placeholder="0.00">
                            </div>

                            {{-- Selling Price --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="selling_price" placeholder="0.00">
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Save Product</button>
                                <a href="{{ route('all.product') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function () {

    // Manage stock toggle
    $('#manageStockToggle').on('change', function () {
        if ($(this).is(':checked')) {
            $('#stockFields').removeClass('d-none');
        } else {
            $('#stockFields').addClass('d-none');
        }
    });

    // Load subcategories on category change
    $('#categorySelect').on('change', function () {
        const categoryId = $(this).val();
        const subSelect  = $('#subcategorySelect');
        subSelect.html('<option value="">-- Select Subcategory --</option>');

        if (categoryId) {
            $.ajax({
                url: '/get/subcategories/' + categoryId,
                type: 'GET',
                success: function (data) {
                    if (data.length > 0) {
                        $.each(data, function (i, sub) {
                            subSelect.append('<option value="' + sub.id + '">' + sub.category_name + '</option>');
                        });
                    }
                }
            });
        }
    });

    // Validation
    $('#myForm').validate({
        rules: {
            product_name:   { required: true },
            category_id:    { required: true },
            brand_id:       { required: true },
            supplier_id:    { required: true },
            warehouse_id:   { required: true },
            purchase_price: { required: true, min: 0 },
            selling_price:  { required: true, min: 0 },
        },
        messages: {
            product_name:   { required: 'Please enter product name' },
            category_id:    { required: 'Please select a category' },
            brand_id:       { required: 'Please select a brand' },
            supplier_id:    { required: 'Please select a supplier' },
            warehouse_id:   { required: 'Please select a warehouse' },
            purchase_price: { required: 'Please enter purchase price' },
            selling_price:  { required: 'Please enter selling price' },
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight:   function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); },
    });
});
</script>

@endsection