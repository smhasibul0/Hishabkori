@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">

    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Edit Supplier</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item active">Edit Supplier</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Supplier</h5>
                    </div>

                    <div class="card-body">
                        <form id="myForm" action="{{ route('update.supplier', $supplier->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
                            @csrf

                            <input type="hidden" name="id" value="{{ $supplier->id }}">

                            {{-- Hidden field to track which column to save to --}}
                            <input type="hidden" name="name_type" value="{{ $supplier->business_name ? 'business_name' : 'name' }}">

                            <div class="form-group col-md-6">
                                <label class="form-label">
                                    {{ $supplier->business_name ? 'Business Name' : 'Supplier Name' }}
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="supplier_name_value"
                                    value="{{ $supplier->business_name ?: $supplier->name }}"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Supplier Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $supplier->email }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Supplier Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ $supplier->phone }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Supplier Address</label>
                                <input type="text" class="form-control" name="address" value="{{ $supplier->address }}">
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                supplier_name_value: {
                    required: true,
                },
                phone: {
                    required: true,
                },
            },
            messages: {
                supplier_name_value: {
                    required: 'Please Enter Supplier Name',
                },
                phone: {
                    required: 'Please Enter Supplier Phone',
                },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

@endsection