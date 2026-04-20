@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Edit Customer</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item active">Edit Customer</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Customer</h5>
                    </div>
                    <div class="card-body">
                        <form id="myForm" action="{{ route('update.customer', $customer->id) }}" method="POST" class="row g-3">
                            @csrf

                            <input type="hidden" name="id" value="{{ $customer->id }}">
                            <input type="hidden" name="name_type" value="{{ $customer->business_name ? 'business_name' : 'name' }}">

                            {{-- Name field: shows whichever column is filled --}}
                            <input type="hidden" name="name_type" value="{{ $customer->business_name ? 'business_name' : 'individual' }}">

                            {{-- Row 1: Type toggle --}}
                            <div class="col-md-12">
                                <label class="form-label">Customer Type</label>
                                <div class="btn-group w-25" role="group">
                                    <input type="radio" class="btn-check" name="edit_type" id="editBusiness" value="business" autocomplete="off"
                                        {{ $customer->business_name ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="editBusiness">Business</label>

                                    <input type="radio" class="btn-check" name="edit_type" id="editIndividual" value="individual" autocomplete="off"
                                        {{ $customer->business_name ? '' : 'checked' }}>
                                    <label class="btn btn-outline-primary" for="editIndividual">Individual</label>
                                </div>
                            </div>

                            {{-- Row 2: Business name (shown if business) --}}
                            <div class="form-group col-md-12 {{ $customer->business_name ? '' : 'd-none' }}" id="editBusinessNameRow">
                                <label class="form-label">Business Name</label>
                                <input type="text" class="form-control" name="business_name" value="{{ $customer->business_name }}">
                            </div>

                            {{-- Row 2: Individual name fields (shown if individual) --}}
                            <div class="col-md-12 {{ $customer->business_name ? 'd-none' : '' }}" id="editIndividualNameRow">
                                <div class="row g-3">
                                    <div class="form-group col-md-2">
                                        <label class="form-label">Prefix</label>
                                        <select class="form-control" name="prefix">
                                            <option value="">--</option>
                                            <option value="Mr."  {{ $customer->prefix === 'Mr.'  ? 'selected' : '' }}>Mr.</option>
                                            <option value="Mrs." {{ $customer->prefix === 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                            <option value="Ms."  {{ $customer->prefix === 'Ms.'  ? 'selected' : '' }}>Ms.</option>
                                            <option value="Dr."  {{ $customer->prefix === 'Dr.'  ? 'selected' : '' }}>Dr.</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="{{ $customer->first_name }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="{{ $customer->middle_name }}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ $customer->last_name }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Customer Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $customer->email }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="form-label">Customer Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ $customer->phone }}">
                            </div>

                            {{-- Customer Group dropdown --}}
                            <div class="form-group col-md-6">
                                <label class="form-label">Customer Group</label>
                                <select class="form-control" name="customer_group">
                                    <option value="">-- Select Customer Group --</option>
                                    @foreach($customerGroups as $group)
                                        <option value="{{ $group->name }}"
                                            {{ $customer->customer_group === $group->name ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pay Term</label>
                                <input type="text" class="form-control" name="pay_term" value="{{ $customer->pay_term }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="openning_balance" value="{{ $customer->openning_balance }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Credit Limit</label>
                                <input type="number" step="0.01" class="form-control" name="credit_limit" value="{{ $customer->credit_limit }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tax Number</label>
                                <input type="text" class="form-control" name="tax_number" value="{{ $customer->tax_number }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" value="{{ $customer->address }}">
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