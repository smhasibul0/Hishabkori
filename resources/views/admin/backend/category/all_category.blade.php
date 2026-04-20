@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">All Product Category</h4>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    Add Category
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
                                    <th>Sl</th>
                                    <th>Category Name</th>
                                    <th>Category Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($category as $key => $item)
                                {{-- Parent row --}}
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><strong>{{ $item->category_name }}</strong></td>
                                    <td>{{ $item->category_code }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal"
                                            data-id="{{ $item->id }}"
                                            data-name="{{ $item->category_name }}"
                                            data-code="{{ $item->category_code }}"
                                            data-description="{{ $item->description }}"
                                            data-parent="{{ $item->parent_id }}">
                                            Edit
                                        </button>
                                        <a href="{{ route('delete.category', $item->id) }}"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Deleting this will also delete all subcategories. Continue?')">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                {{-- Subcategory rows --}}
                                @foreach ($item->children as $child)
                                <tr>
                                    <td></td>
                                    <td class="ps-4 text-muted">{{ $item->category_name }} -> {{ $child->category_name }}</td>
                                    <td>{{ $child->category_code }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal"
                                            data-id="{{ $child->id }}"
                                            data-name="{{ $child->category_name }}"
                                            data-code="{{ $child->category_code }}"
                                            data-description="{{ $child->description }}"
                                            data-parent="{{ $child->parent_id }}">
                                            Edit
                                        </button>
                                        <a href="{{ route('delete.category', $child->id) }}"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure?')">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
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
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Add</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('store.category') }}" method="POST">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category name: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="category_name" placeholder="Category name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Code:</label>
                        <input type="text" class="form-control" name="category_code" placeholder="Category Code">
                        <small class="text-muted">Category code is same as <strong>HSN code</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description:</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Description"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_sub" id="addIsSubCheck" value="1">
                            <label class="form-check-label fw-semibold" for="addIsSubCheck">
                                Add as sub taxonomy
                            </label>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="addParentDropdown">
                        <label class="form-label fw-semibold">Select parent category:</label>
                        <select class="form-control" name="parent_id">
                            <option value="">-- Select Category --</option>
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
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
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('update.category') }}" method="POST">
                @csrf
                <input type="hidden" name="cat_id" id="editCatId">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category name: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="category_name" id="editCatName" placeholder="Category name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Code:</label>
                        <input type="text" class="form-control" name="category_code" id="editCatCode" placeholder="Category Code">
                        <small class="text-muted">Category code is same as <strong>HSN code</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description:</label>
                        <textarea class="form-control" name="description" id="editCatDesc" rows="3" placeholder="Description"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_sub" id="editIsSubCheck" value="1">
                            <label class="form-check-label fw-semibold" for="editIsSubCheck">
                                Add as sub category
                            </label>
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="editParentDropdown">
                        <label class="form-label fw-semibold">Select parent category:</label>
                        <select class="form-control" name="parent_id" id="editParentSelect">
                            <option value="">-- Select Category --</option>
                            @foreach ($category as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
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
    // ── Add modal: toggle parent dropdown on checkbox ──
    document.getElementById('addIsSubCheck').addEventListener('change', function () {
        const dropdown = document.getElementById('addParentDropdown');
        dropdown.classList.toggle('d-none', !this.checked);
    });

    // ── Edit modal: toggle parent dropdown on checkbox ──
    document.getElementById('editIsSubCheck').addEventListener('change', function () {
        const dropdown = document.getElementById('editParentDropdown');
        dropdown.classList.toggle('d-none', !this.checked);
    });

    // ── Fill edit modal from data attributes ──
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const parentId = this.dataset.parent;

            document.getElementById('editCatId').value       = this.dataset.id;
            document.getElementById('editCatName').value     = this.dataset.name;
            document.getElementById('editCatCode').value     = this.dataset.code;
            document.getElementById('editCatDesc').value     = this.dataset.description;

            const isSubCheck   = document.getElementById('editIsSubCheck');
            const parentDropdown = document.getElementById('editParentDropdown');
            const parentSelect = document.getElementById('editParentSelect');

            if (parentId && parentId !== 'null' && parentId !== '') {
                isSubCheck.checked = true;
                parentDropdown.classList.remove('d-none');
                parentSelect.value = parentId;
            } else {
                isSubCheck.checked = false;
                parentDropdown.classList.add('d-none');
                parentSelect.value = '';
            }
        });
    });
</script>

@endsection