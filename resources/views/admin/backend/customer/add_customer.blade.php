@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Add Customer</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item active">Add Customer</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Customer</h5>
                    </div>
                    <div class="card-body">
                        <form id="myForm" action="{{ route('store.customer') }}" method="POST" class="row g-3">
                            @csrf

                            {{-- Row 1: Type toggle + Dynamic name --}}
                            {{-- Row 1: Type toggle --}}
                            <div class="col-md-12 d-flex flex-column">
                                <label class="form-label">Please select customer type</label>
                                <div class="btn-group w-25" role="group">
                                    <input type="radio" class="btn-check" name="supplier_type" id="btnBusiness" value="business" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary" for="btnBusiness">Business</label>
                                    <input type="radio" class="btn-check" name="supplier_type" id="btnIndividual" value="individual" autocomplete="off">
                                    <label class="btn btn-outline-primary" for="btnIndividual">Individual</label>
                                </div>
                            </div>

                            {{-- Row 2: Dynamic name fields --}}

                            {{-- Business name field (shown by default) --}}
                            <div class="form-group col-md-12" id="businessNameRow">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name" placeholder="Enter business name">
                            </div>

                            {{-- Individual name fields (hidden by default) --}}
                            <div class="col-md-12 d-none" id="individualNameRow">
                                <div class="row g-3">
                                    <div class="form-group col-md-2">
                                        <label class="form-label">Prefix</label>
                                        <select class="form-control" name="prefix">
                                            <option value="">--</option>
                                            <option value="Mr.">Mr.</option>
                                            <option value="Mrs.">Mrs.</option>
                                            <option value="Ms.">Ms.</option>
                                            <option value="Dr.">Dr.</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" placeholder="First name">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" placeholder="Middle name">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" placeholder="Last name">
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: Email + Phone --}}
                            <div class="col-md-6">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label">Customer Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>

                            {{-- Row 3: Customer Group + Pay Term --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Customer Group</label>
                                <select class="form-control" name="customer_group">
                                    <option value="">-- Select Customer Group --</option>
                                    @foreach($customerGroups as $group)
                                        <option value="{{ $group->name }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pay Term</label>
                                <input type="text" class="form-control" name="pay_term" placeholder="e.g. Net 30">
                            </div>

                            {{-- Row 4: Opening Balance + Credit Limit --}}
                            <div class="col-md-6">
                                <label class="form-label">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="openning_balance" value="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Credit Limit</label>
                                <input type="number" step="0.01" class="form-control" name="credit_limit" placeholder="Enter credit limit">
                            </div>

                            {{-- Row 5: Tax Number + Address --}}
                            <div class="col-md-6">
                                <label class="form-label">Tax Number</label>
                                <input type="text" class="form-control" name="tax_number" placeholder="Enter tax number">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Enter address">
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

        $('input[name="supplier_type"]').on('change', function () {
            if ($(this).val() === 'business') {
                $('#businessNameRow').removeClass('d-none');
                $('#individualNameRow').addClass('d-none');
                // clear individual fields
                $('[name="prefix"]').val('');
                $('[name="first_name"]').val('');
                $('[name="middle_name"]').val('');
                $('[name="last_name"]').val('');
            } else {
                $('#businessNameRow').addClass('d-none');
                $('#individualNameRow').removeClass('d-none');
                // clear business field
                $('[name="business_name"]').val('');
            }
        });

        $('#myForm').validate({
            ignore: ':hidden',
            rules: {
                business_name: { required: true },
                first_name:    { required: true },
                phone:         { required: true },
            },
            messages: {
                business_name: { required: 'Please enter business name' },
                first_name:    { required: 'Please enter first name' },
                phone:         { required: 'Please enter customer phone' },
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