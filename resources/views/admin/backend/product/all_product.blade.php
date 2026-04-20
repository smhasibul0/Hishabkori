@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">All Products</h4>
            </div>
            <div class="text-end">
                <a href="{{ route('add.product') }}" class="btn btn-secondary">Add Product</a>
            </div>
        </div>

        <style>
            .dt-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
            .dt-controls { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem; padding:.85rem 1rem; border-bottom:1px solid #e2e8f0; background:#fff; }
            .dt-controls-left { display:flex; align-items:center; gap:.4rem; font-size:.8rem; color:#64748b; }
            .dt-controls-left select { border:1px solid #cbd5e1; border-radius:5px; padding:.25rem .4rem; font-size:.8rem; color:#334155; outline:none; cursor:pointer; }
            .dt-search-wrap { position:relative; }
            .dt-search-wrap i { position:absolute; left:.6rem; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.75rem; pointer-events:none; }
            .dt-search { border:1px solid #cbd5e1; border-radius:5px; padding:.3rem .7rem .3rem 1.9rem; font-size:.8rem; color:#334155; width:210px; outline:none; transition:border-color .2s; }
            .dt-search:focus { border-color:#6366f1; }
            .dt-search::placeholder { color:#94a3b8; }
            .dt-scroll-area { overflow-x:auto; overflow-y:visible; scrollbar-width:thin; scrollbar-color:#cbd5e1 transparent; }
            .dt-scroll-area::-webkit-scrollbar { height:5px; }
            .dt-scroll-area::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:99px; }
            .dt-scroll-area::-webkit-scrollbar-thumb:hover { background:#6366f1; }
            #product-table { width:100%; min-width:1400px; border-collapse:collapse; font-size:.8rem; margin-bottom:0; }
            #product-table thead th { background:#f8fafc; color:#475569; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:.6rem .9rem; white-space:nowrap; border-bottom:2px solid #e2e8f0; border-right:1px solid #e2e8f0; position:sticky; top:0; z-index:2; cursor:pointer; user-select:none; }
            #product-table thead th:last-child { border-right:none; }
            #product-table thead th:hover { background:#f1f5f9; color:#1e293b; }
            #product-table thead th.sort-asc::after { content:' ↑'; color:#6366f1; }
            #product-table thead th.sort-desc::after { content:' ↓'; color:#6366f1; }
            #product-table tbody tr { border-bottom:1px solid #e2e8f0; transition:background .1s; }
            #product-table tbody tr:nth-child(even) { background:#f8fafc; }
            #product-table tbody tr:hover { background:#eef2ff; }
            #product-table tbody tr.stock-alert { background:#fff1f1 !important; }
            #product-table tbody tr.stock-alert:hover { background:#ffe4e4 !important; }
            #product-table tbody td { padding:.55rem .9rem; white-space:nowrap; color:#334155; border-right:1px solid #e2e8f0; }
            #product-table tbody td:last-child { border-right:none; }
            #product-table tfoot td { background:#f1f5f9; font-weight:700; color:#1e293b; padding:.55rem .9rem; border-top:2px solid #e2e8f0; }
            .dt-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem; padding:.8rem 1rem; border-top:1px solid #e2e8f0; background:#fff; }
            .dt-info { font-size:.75rem; color:#64748b; }
            .dt-pagination { display:flex; gap:.2rem; }
            .dt-pagination button { border:1px solid #cbd5e1; background:#fff; color:#475569; font-size:.75rem; padding:.28rem .6rem; border-radius:5px; cursor:pointer; min-width:30px; transition:all .15s; }
            .dt-pagination button:hover:not(:disabled) { border-color:#6366f1; color:#6366f1; }
            .dt-pagination button.active { background:#6366f1; border-color:#6366f1; color:#fff; font-weight:600; }
            .dt-pagination button:disabled { opacity:.35; cursor:default; }
            .no-results { text-align:center; padding:2.5rem 1rem; color:#94a3b8; font-size:.85rem; }
            .product-thumb { width:45px; height:45px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0; }
            .badge-active { background:#dcfce7; color:#166534; padding:.2rem .5rem; border-radius:4px; font-size:.7rem; font-weight:600; }
            .badge-inactive { background:#fee2e2; color:#991b1b; padding:.2rem .5rem; border-radius:4px; font-size:.7rem; font-weight:600; }
            .stock-alert-badge { background:#fef3c7; color:#92400e; padding:.15rem .4rem; border-radius:4px; font-size:.65rem; font-weight:700; margin-left:.3rem; }
        </style>

        <div class="row">
            <div class="col-12">
                <div class="card p-0 border-0 shadow-none">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="dt-card">

                            <div class="dt-controls">
                                <div class="dt-controls-left">
                                    Show
                                    <select id="pageSizeSelect">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    entries
                                </div>
                                <div class="dt-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input class="dt-search" id="searchInput" type="text" placeholder="Search…">
                                </div>
                            </div>

                            <div class="dt-scroll-area">
                                <table id="product-table">
                                    <thead>
                                        <tr>
                                            <th data-col="0" data-sortable="false">Image</th>
                                            <th data-col="1" data-sortable="false">Action</th>
                                            <th data-col="2">Product Name</th>
                                            <th data-col="3">Category</th>
                                            <th data-col="4">Subcategory</th>
                                            <th data-col="5">Brand</th>
                                            <th data-col="6">Warehouse</th>
                                            <th data-col="7">Purchase Price</th>
                                            <th data-col="8">Sale Price</th>
                                            <th data-col="9">Quantity</th>
                                            <th data-col="10">Product Code</th>
                                            <th data-col="11">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @foreach($products as $item)
                                        @php
                                            $images     = json_decode($item->image, true) ?? [];
                                            $firstImage = $images[0] ?? null;
                                            $isAlert    = $item->manage_stock && $item->alert_quantity > 0 && $item->quantity <= $item->alert_quantity;
                                        @endphp
                                        <tr class="{{ $isAlert ? 'stock-alert' : '' }}">
                                            <td>
                                                @if($firstImage)
                                                    <img src="{{ asset($firstImage) }}" class="product-thumb" alt="{{ $item->product_name }}">
                                                @else
                                                    <div style="width:45px;height:45px;background:#f1f5f9;border-radius:6px;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;">
                                                        <i class="fas fa-image" style="color:#94a3b8;font-size:.8rem;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Action <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#">View</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{ route('edit.product', $item->id) }}">Edit</a>
                                                    <a href="{{ route('delete.product', $item->id) }}" class="dropdown-item" id="delete">Delete</a>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $item->product_name }}
                                                @if($isAlert)
                                                    <span class="stock-alert-badge">⚠ Low Stock</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->category->category_name ?? '—' }}</td>
                                            <td>{{ $item->subcategory->category_name ?? '—' }}</td>
                                            <td>{{ $item->brand->brand_name ?? '—' }}</td>
                                            <td>{{ $item->warehouse->name ?? '—' }}</td>
                                            <td>৳ {{ number_format($item->purchase_price, 2) }}</td>
                                            <td>৳ {{ number_format($item->selling_price, 2) }}</td>
                                            <td>
                                                @if($item->manage_stock)
                                                    {{ $item->quantity }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->product_code }}</td>
                                            <td>
                                                @if($item->is_active)
                                                    <span class="badge-active">Active</span>
                                                @else
                                                    <span class="badge-inactive">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-end pe-3">Total Products:</td>
                                            <td colspan="5">{{ $products->count() }} products</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="dt-footer">
                                <div class="dt-info" id="tableInfo">Loading…</div>
                                <div class="dt-pagination" id="pagination"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody     = document.getElementById('tableBody');
    const allRows   = Array.from(tbody.querySelectorAll('tr'));
    const totalRows = allRows.length;
    let filtered    = [...allRows];
    let currentPage = 1;
    let pageSize    = 10;
    let sortCol     = -1;
    let sortDir     = 1;

    function cellText(row, col) {
        const cell = row.querySelectorAll('td')[col];
        return cell ? cell.innerText.trim() : '';
    }

    function render() {
        allRows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * pageSize;
        const end   = Math.min(start + pageSize, filtered.length);

        if (filtered.length === 0) {
            if (!document.getElementById('no-results-row')) {
                const tr = document.createElement('tr');
                tr.id = 'no-results-row';
                tr.innerHTML = `<td colspan="12" class="no-results">No matching records found</td>`;
                tbody.appendChild(tr);
            }
            document.getElementById('no-results-row').style.display = '';
        } else {
            const noRes = document.getElementById('no-results-row');
            if (noRes) noRes.style.display = 'none';
            filtered.slice(start, end).forEach(r => r.style.display = '');
        }

        document.getElementById('tableInfo').textContent = filtered.length === 0
            ? 'No entries found'
            : `Showing ${start + 1} to ${end} of ${filtered.length} entries`
              + (filtered.length < totalRows ? ` (filtered from ${totalRows} total)` : '');

        renderPagination();
    }

    function renderPagination() {
        const pag        = document.getElementById('pagination');
        const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
        pag.innerHTML    = '';

        const btn = (label, page, disabled = false, active = false) => {
            const b = document.createElement('button');
            b.innerHTML = label;
            b.disabled  = disabled;
            if (active) b.classList.add('active');
            b.onclick = () => { currentPage = page; render(); };
            return b;
        };

        pag.appendChild(btn('‹', currentPage - 1, currentPage === 1));
        const delta = 2;
        let prev = null;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
                if (prev !== null && i - prev > 1) {
                    const dots = document.createElement('button');
                    dots.textContent = '…'; dots.disabled = true;
                    pag.appendChild(dots);
                }
                pag.appendChild(btn(i, i, false, i === currentPage));
                prev = i;
            }
        }
        pag.appendChild(btn('›', currentPage + 1, currentPage === totalPages));
    }

    document.getElementById('searchInput').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        filtered = allRows.filter(row =>
            Array.from(row.querySelectorAll('td')).slice(2).some(td => td.innerText.toLowerCase().includes(q))
        );
        currentPage = 1;
        render();
    });

    document.getElementById('pageSizeSelect').addEventListener('change', function () {
        pageSize = parseInt(this.value);
        currentPage = 1;
        render();
    });

    document.querySelectorAll('#product-table thead th').forEach(th => {
        if (th.dataset.sortable === 'false') return;
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
            sortDir   = (sortCol === col) ? sortDir * -1 : 1;
            sortCol   = col;
            document.querySelectorAll('#product-table thead th').forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            th.classList.add(sortDir === 1 ? 'sort-asc' : 'sort-desc');
            filtered.sort((a, b) => {
                const av = cellText(a, col).replace(/[৳,]/g, '');
                const bv = cellText(b, col).replace(/[৳,]/g, '');
                const an = parseFloat(av), bn = parseFloat(bv);
                if (!isNaN(an) && !isNaN(bn)) return (an - bn) * sortDir;
                return av.localeCompare(bv) * sortDir;
            });
            filtered.forEach(r => tbody.appendChild(r));
            currentPage = 1;
            render();
        });
    });

    render();
});
</script>

@endsection