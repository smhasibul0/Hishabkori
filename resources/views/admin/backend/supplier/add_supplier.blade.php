@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Add Supplier</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item active">Add Supplier</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Supplier</h5>
                    </div>

                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.supplier') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                            @csrf

                            {{-- Row 1: Radio buttons (left) + Dynamic Name field (right) --}}
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <label class="form-label">Please select supplier type</label>
                                <div class="btn-group w-100" role="group" aria-label="Supplier type toggle">
                                    <input type="radio" class="btn-check" name="supplier_type" id="btnBusiness" value="business" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary w-50" for="btnBusiness">Business</label>

                                    <input type="radio" class="btn-check" name="supplier_type" id="btnIndividual" value="individual" autocomplete="off">
                                    <label class="btn btn-outline-primary w-50" for="btnIndividual">Individual</label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label id="nameLabel" for="supplierName" class="form-label">Business Name</label>
                                <input type="text" class="form-control" id="supplierName" name="business_name" placeholder="Enter business name">
                            </div>

                            {{-- Row 2: Email + Phone --}}
                            <div class="col-md-6">
                                <label class="form-label">Supplier Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label">Supplier Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>

                            {{-- Row 3: Address --}}
                            <div class="col-md-6">
                                <label class="form-label">Supplier Address</label>
                                <input type="text" class="form-control" name="address">
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
    $(document).ready(function () {

        // Switch label, placeholder and name attribute when radio changes
        $('input[name="supplier_type"]').on('change', function () {
            if ($(this).val() === 'business') {
                $('#nameLabel').text('Business Name');
                $('#supplierName').attr('name', 'business_name');
                $('#supplierName').attr('placeholder', 'Enter business name');
            } else {
                $('#nameLabel').text('Individual Name');
                $('#supplierName').attr('name', 'name');
                $('#supplierName').attr('placeholder', 'Enter individual name');
            }
        });

        // Form validation
        $('#myForm').validate({
            rules: {
                business_name: { required: true },
                name: { required: true },
            },
            messages: {
                business_name: { required: 'Please enter business name' },
                name: { required: 'Please enter individual name' },
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>

@endsection