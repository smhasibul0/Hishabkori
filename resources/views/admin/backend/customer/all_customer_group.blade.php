@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">All Customer Groups</h4>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addCustomerGroupModal">
                    Add Customer Group
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Customer Group Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer_groups as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        {{-- Edit button triggers modal and fills data --}}
                                        <button class="btn btn-sm btn-success edit-btn"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCustomerGroupModal">
                                            Edit
                                        </button>
                                        <a href="{{ route('delete.customer_group', $item->id) }}" class="btn btn-sm btn-danger" id="delete">Delete</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- ==================== ADD MODAL ==================== --}}
<div class="modal fade" id="addCustomerGroupModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="addModalLabel">Add Customer Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addForm" action="{{ route('store.customer_group') }}" method="POST">
                @csrf
                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Group Name: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_group_name" placeholder="Customer Group Name">
                        <span class="invalid-feedback d-block" id="addNameError"></span>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-4">Save</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ==================== EDIT MODAL ==================== --}}
<div class="modal fade" id="editCustomerGroupModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="editModalLabel">Edit Customer Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editForm" action="" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body pt-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Group Name: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_group_name" id="editGroupName" placeholder="Customer Group Name">
                        <span class="invalid-feedback d-block" id="editNameError"></span>
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-4">Save</button>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
    // Fill edit modal with row data
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('editGroupName').value = name;
            document.getElementById('editForm').action = '/update/customer-group/' + id;
        });
    });

    // Simple client-side required validation for Add
    document.getElementById('addForm').addEventListener('submit', function (e) {
        const nameInput = this.querySelector('[name="customer_group_name"]');
        const error     = document.getElementById('addNameError');
        if (nameInput.value.trim() === '') {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            error.textContent = 'Please enter customer group name';
        } else {
            nameInput.classList.remove('is-invalid');
            error.textContent = '';
        }
    });

    // Simple client-side required validation for Edit
    document.getElementById('editForm').addEventListener('submit', function (e) {
        const nameInput = this.querySelector('[name="customer_group_name"]');
        const error     = document.getElementById('editNameError');
        if (nameInput.value.trim() === '') {
            e.preventDefault();
            nameInput.classList.add('is-invalid');
            error.textContent = 'Please enter customer group name';
        } else {
            nameInput.classList.remove('is-invalid');
            error.textContent = '';
        }
    });
</script>

@endsection