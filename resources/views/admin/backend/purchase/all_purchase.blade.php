@extends('admin.admin_master')
@section('admin')

<div class="content">
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">All Purchases</h4>
            </div>
            <div class="text-end">
                <a href="{{ route('add.purchase') }}" class="btn btn-secondary">Add Purchase</a>
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
            #purchase-table { width:100%; min-width:1200px; border-collapse:collapse; font-size:.8rem; margin-bottom:0; }
            #purchase-table thead th { background:#f8fafc; color:#475569; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:.6rem .9rem; white-space:nowrap; border-bottom:2px solid #e2e8f0; border-right:1px solid #e2e8f0; position:sticky; top:0; z-index:2; cursor:pointer; user-select:none; }
            #purchase-table thead th:last-child { border-right:none; }
            #purchase-table thead th:hover { background:#f1f5f9; color:#1e293b; }
            #purchase-table thead th.sort-asc::after { content:' ↑'; color:#6366f1; }
            #purchase-table thead th.sort-desc::after { content:' ↓'; color:#6366f1; }
            #purchase-table tbody tr { border-bottom:1px solid #e2e8f0; transition:background .1s; }
            #purchase-table tbody tr:nth-child(even) { background:#f8fafc; }
            #purchase-table tbody tr:hover { background:#eef2ff; }
            #purchase-table tbody tr.overdue-alert { background:#fff1f1 !important; }
            #purchase-table tbody tr.overdue-alert:hover { background:#ffe4e4 !important; }
            #purchase-table tbody td { padding:.55rem .9rem; white-space:nowrap; color:#334155; border-right:1px solid #e2e8f0; }
            #purchase-table tbody td:last-child { border-right:none; }
            #purchase-table tfoot td { background:#f1f5f9; font-weight:700; color:#1e293b; padding:.55rem .9rem; border-top:2px solid #e2e8f0; }
            .dt-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem; padding:.8rem 1rem; border-top:1px solid #e2e8f0; background:#fff; }
            .dt-info { font-size:.75rem; color:#64748b; }
            .dt-pagination { display:flex; gap:.2rem; }
            .dt-pagination button { border:1px solid #cbd5e1; background:#fff; color:#475569; font-size:.75rem; padding:.28rem .6rem; border-radius:5px; cursor:pointer; min-width:30px; transition:all .15s; }
            .dt-pagination button:hover:not(:disabled) { border-color:#6366f1; color:#6366f1; }
            .dt-pagination button.active { background:#6366f1; border-color:#6366f1; color:#fff; font-weight:600; }
            .dt-pagination button:disabled { opacity:.35; cursor:default; }
            .no-results { text-align:center; padding:2.5rem 1rem; color:#94a3b8; font-size:.85rem; }
            .badge-status { display:inline-flex; align-items:center; padding:.2rem .5rem; border-radius:4px; font-size:.7rem; font-weight:600; }
            .badge-received, .badge-paid   { background:#dcfce7; color:#166534; }
            .badge-ordered,  .badge-partial { background:#dbeafe; color:#1d4ed8; }
            .badge-pending,  .badge-due     { background:#fee2e2; color:#991b1b; }
            .mono { font-family:'Courier New', monospace; font-size:.82rem; }
            .action-dd-menu { min-width:180px; font-size:.8rem; }
        </style>

        <div class="row">
            <div class="col-12">
                <div class="card p-0 border-0 shadow-none">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Purchase List</h5>
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
                                <table id="purchase-table">
                                    <thead>
                                        <tr>
                                            <th data-col="0" data-sortable="false">Action</th>
                                            <th data-col="1">Date</th>
                                            <th data-col="2">Reference No</th>
                                            <th data-col="3">Location</th>
                                            <th data-col="4">Supplier</th>
                                            <th data-col="5">Purchase Status</th>
                                            <th data-col="6">Payment Status</th>
                                            <th data-col="7">Grand Total</th>
                                            <th data-col="8">Payment Due</th>
                                            <th data-col="9">Added By</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBody">
                                        @forelse($purchases as $purchase)
                                        @php
                                            $isDue = $purchase->payment_status === 'due';
                                        @endphp
                                        @php
                                            $supplierName = $purchase->supplier?->business_name
                                                         ?: $purchase->supplier?->name
                                                         ?? '—';
                                        @endphp
                                        <tr class="{{ $isDue ? 'overdue-alert' : '' }}">
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Action <i class="mdi mdi-chevron-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu action-dd-menu shadow-sm">
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               data-bs-toggle="modal" data-bs-target="#viewModal"
                                                               data-url="{{ route('purchases.show', $purchase) }}">
                                                                <i class="bi bi-eye me-2 text-muted"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('edit.purchase', $purchase) }}">
                                                                <i class="bi bi-pencil me-2 text-muted"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger"
                                                               href="{{ route('delete.purchase', $purchase) }}"
                                                               onclick="return confirm('Are you sure?')">
                                                                <i class="bi bi-trash me-2"></i> Delete
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('purchases.print', $purchase) }}" target="_blank">
                                                                <i class="bi bi-printer me-2 text-muted"></i> Print
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('purchases.download-document', $purchase) }}">
                                                                <i class="bi bi-download me-2 text-muted"></i> Download Document
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('purchases.view-document', $purchase) }}" target="_blank">
                                                                <i class="bi bi-file-earmark-pdf me-2 text-muted"></i> View Document
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               data-bs-toggle="modal" data-bs-target="#addPaymentModal"
                                                               data-purchase-id="{{ $purchase->id }}"
                                                               data-reference="{{ $purchase->reference_no }}"
                                                               data-supplier="{{ $purchase->supplier?->name }}"
                                                               data-location="{{ $purchase->location?->name }}"
                                                               data-total="{{ number_format($purchase->grand_total, 2) }}"
                                                               data-due="{{ number_format($purchase->payment_due, 2) }}"
                                                               data-url="{{ route('purchases.add-payment', $purchase) }}">
                                                                <i class="bi bi-cash-coin me-2 text-muted"></i> Add Payment
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               data-bs-toggle="modal" data-bs-target="#viewPaymentsModal"
                                                               data-url="{{ route('purchases.view-payments', $purchase) }}">
                                                                <i class="bi bi-bar-chart-line me-2 text-muted"></i> View Payments
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>{{ optional($purchase->purchase_date)->format('d-m-Y h:i A') }}</td>
                                            <td><strong>{{ $purchase->reference_no }}</strong></td>
                                            <td>{{ $purchase->location?->name ?? '—' }}</td>
                                            <td>{{ $supplierName }}</td>
                                            <td>
                                                <span class="badge-status badge-{{ $purchase->purchase_status }}">
                                                    {{ ucfirst($purchase->purchase_status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-status badge-{{ $purchase->payment_status }}">
                                                    {{ ucfirst($purchase->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="mono">৳ {{ number_format($purchase->grand_total, 2) }}</td>
                                            <td class="mono">৳ {{ number_format($purchase->payment_due, 2) }}</td>
                                            <td>{{ $purchase->addedBy?->name ?? '—' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="no-results">No purchases found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6" class="text-end pe-3">Total Purchases:</td>
                                            <td colspan="4">{{ $purchases->count() }} purchases</td>
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

{{-- ══════════════════════════════════════════
     MODAL: View Purchase Details
══════════════════════════════════════════ --}}
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Add Payment
══════════════════════════════════════════ --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPaymentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-2 mb-3" id="paymentMeta"></div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Payment Method *</label>
                            <select name="payment_method" class="form-select form-select-sm" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Paid On *</label>
                            <input type="datetime-local" name="paid_on" class="form-control form-control-sm" required
                                   value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Amount *</label>
                            <input type="number" name="amount" id="payAmount" class="form-control form-control-sm" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Payment Account</label>
                            <select name="payment_account_id" class="form-select form-select-sm">
                                <option value="">Select Account</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Attach Document</label>
                            <input type="file" name="document" class="form-control form-control-sm"
                                   accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png">
                            <small class="text-muted">Allowed: .pdf, .csv, .zip, .doc, .docx, .jpeg, .jpg, .png</small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Payment Note</label>
                        <textarea name="payment_note" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: View Payments
══════════════════════════════════════════ --}}
<div class="modal fade" id="viewPaymentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="vpModalTitle">View Payments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewPaymentsBody">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
                tr.innerHTML = `<td colspan="10" class="no-results">No matching records found</td>`;
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
            Array.from(row.querySelectorAll('td')).slice(1).some(td => td.innerText.toLowerCase().includes(q))
        );
        currentPage = 1;
        render();
    });

    document.getElementById('pageSizeSelect').addEventListener('change', function () {
        pageSize = parseInt(this.value);
        currentPage = 1;
        render();
    });

    document.querySelectorAll('#purchase-table thead th').forEach(th => {
        if (th.dataset.sortable === 'false') return;
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
            sortDir   = (sortCol === col) ? sortDir * -1 : 1;
            sortCol   = col;
            document.querySelectorAll('#purchase-table thead th').forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
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

/* ── VIEW MODAL ── */
document.getElementById('viewModal').addEventListener('show.bs.modal', function (e) {
    const url  = e.relatedTarget.dataset.url;
    const body = document.getElementById('viewModalBody');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(p => { body.innerHTML = buildViewHTML(p); })
        .catch(() => { body.innerHTML = '<p class="text-danger text-center">Failed to load.</p>'; });
});

function buildViewHTML(p) {
    const supplier = p.supplier || {};
    const location = p.location || {};

    const itemRows = (p.items || []).map((it, i) => `
        <tr>
            <td>${i+1}</td>
            <td>${it.product?.name || '—'}</td>
            <td>${it.sku || '—'}</td>
            <td>${it.purchase_quantity} ${it.unit}</td>
            <td class="mono">৳ ${parseFloat(it.unit_cost_before_discount||0).toFixed(2)}</td>
            <td>${parseFloat(it.discount_percent||0).toFixed(2)}%</td>
            <td class="mono">৳ ${parseFloat(it.unit_cost_before_tax||0).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.subtotal_before_tax||0).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.tax_amount||0).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.unit_cost_after_tax||0).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.subtotal||0).toFixed(2)}</td>
        </tr>`).join('') || '<tr><td colspan="11" class="text-center text-muted">No items</td></tr>';

    const payRows = (p.payments || []).map((pay, i) => `
        <tr>
            <td>${i+1}</td>
            <td>${pay.paid_on ? pay.paid_on.substring(0,10) : '—'}</td>
            <td>${pay.reference_no || '—'}</td>
            <td class="mono">৳ ${parseFloat(pay.amount).toFixed(2)}</td>
            <td>${pay.payment_method || '—'}</td>
            <td>${pay.payment_note || '—'}</td>
            <td>${pay.payment_account?.name || '—'}</td>
        </tr>`).join('') || '<tr><td colspan="7" class="text-center text-muted py-3">No payments found</td></tr>';

    const grandTotal = parseFloat(p.grand_total || 0);
    const totalPaid  = (p.payments || []).reduce((s, x) => s + parseFloat(x.amount), 0);

    return `
    <div class="row mb-3 small">
        <div class="col-md-4">
            <strong>Supplier:</strong><br>
            ${supplier.name || '—'}<br>
            Mobile: ${supplier.phone || '—'}
        </div>
        <div class="col-md-4">
            <strong>Business:</strong><br>
            Quick Shifter – ${location.name || '—'}<br>
            Mobile: +880 1787871041<br>
            Email: quickshifter21@gmail.com
        </div>
        <div class="col-md-4 text-end">
            <strong>Reference No:</strong> #${p.reference_no}<br>
            <strong>Date:</strong> ${p.date ? p.date.substring(0,10) : '—'}<br>
            <strong>Purchase Status:</strong> ${p.purchase_status}<br>
            <strong>Payment Status:</strong> ${p.payment_status}
        </div>
    </div>
    <div class="table-responsive mb-3">
        <table class="table table-sm align-middle" style="font-size:.82rem">
            <thead style="background:#22c55e;color:#fff">
                <tr>
                    <th>#</th><th>Product Name</th><th>SKU</th><th>Purchase Qty</th>
                    <th>Unit Cost (Before Disc.)</th><th>Discount %</th><th>Unit Cost (Before Tax)</th>
                    <th>Subtotal (Before Tax)</th><th>Tax</th><th>Unit Cost (After Tax)</th><th>Subtotal</th>
                </tr>
            </thead>
            <tbody>${itemRows}</tbody>
        </table>
    </div>
    <h6 class="fw-bold mb-2">Payment info:</h6>
    <div class="table-responsive mb-3">
        <table class="table table-sm align-middle" style="font-size:.82rem">
            <thead style="background:#22c55e;color:#fff">
                <tr><th>#</th><th>Date</th><th>Reference No</th><th>Amount</th><th>Payment Mode</th><th>Payment Note</th><th>Payment Account</th></tr>
            </thead>
            <tbody>${payRows}</tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end">
        <table style="width:300px;font-size:.875rem">
            <tr><td>Net Total Amount:</td><td class="text-end mono">৳ ${grandTotal.toFixed(2)}</td></tr>
            <tr><td>Discount: (-)</td><td class="text-end mono">৳ 0.00</td></tr>
            <tr><td>Purchase Tax: (+)</td><td class="text-end mono">0.00</td></tr>
            <tr><td>Additional Shipping: (+)</td><td class="text-end mono">৳ ${parseFloat(p.shipping_charges||0).toFixed(2)}</td></tr>
            <tr class="fw-bold border-top"><td>Purchase Total:</td><td class="text-end mono">৳ ${grandTotal.toFixed(2)}</td></tr>
        </table>
    </div>
    <hr>
    <h6 class="fw-bold mb-2">Activities:</h6>
    <table class="table table-sm" style="font-size:.82rem">
        <thead class="table-light"><tr><th>Date</th><th>Action</th><th>By</th><th>Note</th></tr></thead>
        <tbody>
            <tr>
                <td>${p.created_at ? p.created_at.substring(0,16).replace('T',' ') : '—'}</td>
                <td>Added</td>
                <td>${p.added_by?.name || '—'}</td>
                <td>
                    <span class="badge-status badge-${p.purchase_status}">${p.purchase_status}</span>
                    <span class="badge-status" style="background:#dbeafe;color:#1d4ed8">৳ ${grandTotal.toFixed(2)}</span>
                    <span class="badge-status badge-${p.payment_status}">${p.payment_status}</span>
                </td>
            </tr>
        </tbody>
    </table>`;
}

/* ── ADD PAYMENT MODAL ── */
document.getElementById('addPaymentModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const form = document.getElementById('addPaymentForm');

    form.action = btn.dataset.url;
    document.getElementById('payAmount').value = btn.dataset.due;

    document.getElementById('paymentMeta').innerHTML = `
        <div class="col-4">
            <div class="border rounded p-2 small">
                <strong>Supplier:</strong> ${btn.dataset.supplier}<br>
                <strong>Business:</strong> Quick Shifter
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2 small">
                <strong>Reference No:</strong> ${btn.dataset.reference}<br>
                <strong>Location:</strong> ${btn.dataset.location}
            </div>
        </div>
        <div class="col-4">
            <div class="border rounded p-2 small">
                <strong>Total amount:</strong> ৳ ${btn.dataset.total}<br>
                <strong>Due:</strong> ৳ ${btn.dataset.due}
            </div>
        </div>`;
});

document.getElementById('addPaymentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form     = this;
    const formData = new FormData(form);

    fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('addPaymentModal')).hide();
                window.location.reload();
            } else {
                alert('Error saving payment.');
            }
        });
});

/* ── VIEW PAYMENTS MODAL ── */
document.getElementById('viewPaymentsModal').addEventListener('show.bs.modal', function (e) {
    const url  = e.relatedTarget.dataset.url;
    const body = document.getElementById('viewPaymentsBody');
    body.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(p => {
            document.getElementById('vpModalTitle').textContent = `View Payments ( Reference No: ${p.reference_no} )`;
            const supplier = p.supplier || {};
            const location = p.location || {};

            const rows = (p.payments || []).map((pay, i) => `
                <tr>
                    <td>${i+1}</td>
                    <td>${pay.paid_on ? pay.paid_on.substring(0,10) : '—'}</td>
                    <td>${pay.reference_no || '—'}</td>
                    <td class="mono">৳ ${parseFloat(pay.amount).toFixed(2)}</td>
                    <td>${pay.payment_method || '—'}</td>
                    <td>${pay.payment_note || '—'}</td>
                    <td>${pay.payment_account?.name || '—'}</td>
                    <td>
                        <a href="/purchases/${p.id}/payments/${pay.id}/delete"
                           class="btn btn-outline-danger btn-sm"
                           onclick="return confirm('Delete this payment?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>`).join('') || '<tr><td colspan="8" class="text-center text-muted py-4">No records found</td></tr>';

            body.innerHTML = `
                <div class="row mb-3 small">
                    <div class="col-4">
                        <strong>Supplier:</strong><br>${supplier.name || '—'}<br>Mobile: ${supplier.phone || '—'}
                    </div>
                    <div class="col-4">
                        <strong>Business:</strong><br>Quick Shifter – ${location.name || '—'}<br>
                        Mobile: +880 1787871041<br>Email: quickshifter21@gmail.com
                    </div>
                    <div class="col-4 text-end">
                        <strong>Reference No:</strong> #${p.reference_no}<br>
                        <strong>Date:</strong> ${p.date?.substring(0,10) || '—'}<br>
                        <strong>Purchase Status:</strong> ${p.purchase_status}<br>
                        <strong>Payment Status:</strong> ${p.payment_status}
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal"
                        onclick="triggerAddPayment(${p.id}, '${p.reference_no}')">
                        + Add Payment
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle" style="font-size:.82rem">
                        <thead style="background:#22c55e;color:#fff">
                            <tr><th>#</th><th>Date</th><th>Ref No</th><th>Amount</th><th>Method</th><th>Note</th><th>Account</th><th>Actions</th></tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>`;
        });
});
</script>

@endsection