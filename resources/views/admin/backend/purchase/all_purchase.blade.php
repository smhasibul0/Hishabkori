@extends('admin.admin_master')
@section('admin')

@push('styles')
<style>
    .badge-received { background:#dcfce7; color:#15803d; }
    .badge-paid     { background:#dcfce7; color:#15803d; }
    .badge-due      { background:#fff7ed; color:#c2410c; }
    .badge-partial  { background:#fef9c3; color:#92400e; }
    .badge-ordered  { background:#dbeafe; color:#1d4ed8; }
    .badge-pending  { background:#f3f4f6; color:#374151; }
    .badge { display:inline-flex; align-items:center; padding:3px 11px; border-radius:20px; font-size:.78rem; font-weight:600; }
    .action-dd-menu { min-width:200px; }
    .mono { font-family: 'Courier New', monospace; font-size:.85rem; }
</style>
@endpush

<div class="content">
<div class="container-fluid py-4">

    {{-- Page heading --}}
    <h4 class="fw-bold mb-3">All Purchases</h4>

    

    {{-- Main card --}}
    <div class="card shadow-sm">

        {{-- Toolbar --}}
        <div class="card-body border-bottom py-2 d-flex flex-wrap align-items-center gap-2">
            <div class="d-flex align-items-center gap-2 me-2">
                <span class="small text-muted">Show</span>
                <select id="perPage" class="form-select form-select-sm" style="width:80px" onchange="changePerPage(this.value)">
                    @foreach([25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected(request('per_page', 100) == $n)>{{ $n }}</option>
                    @endforeach
                </select>
                <span class="small text-muted">entries</span>
            </div>


        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="purchasesTable">
                <thead class="table-light">
                    <tr>
                        <th>Action</th>
                        <th>Date</th>
                        <th>Reference No</th>
                        <th>Location</th>
                        <th>Supplier</th>
                        <th>Purchase Status</th>
                        <th>Payment Status</th>
                        <th>Grand Total</th>
                        <th>Payment Due</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr data-search="{{ strtolower($purchase->reference_no . ' ' . $purchase->supplier?->name . ' ' . $purchase->location?->name . ' ' . $purchase->addedBy?->name) }}">
                        <td>
                            {{-- Action dropdown --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu action-dd-menu shadow-sm">
                                    {{-- View --}}
                                    <li>
                                        <a class="dropdown-item" href="#"
                                           data-bs-toggle="modal" data-bs-target="#viewModal"
                                           data-url="{{ route('purchases.show', $purchase) }}">
                                            <i class="bi bi-eye me-2 text-muted"></i> View
                                        </a>
                                    </li>

                                    {{-- Edit --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('edit.purchase', $purchase) }}">
                                            <i class="bi bi-pencil me-2 text-muted"></i> Edit
                                        </a>
                                    </li>

                                    {{-- Delete --}}
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('delete.purchase', $purchase) }}" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </a>
                                    </li>


                                    {{-- Print --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('purchases.print', $purchase) }}" target="_blank">
                                            <i class="bi bi-printer me-2 text-muted"></i> Print
                                        </a>
                                    </li>

                                    {{-- Download Document --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('purchases.download-document', $purchase) }}">
                                            <i class="bi bi-download me-2 text-muted"></i> Download Document
                                        </a>
                                    </li>

                                    {{-- View Document --}}
                                    <li>
                                        <a class="dropdown-item" href="{{ route('purchases.view-document', $purchase) }}" target="_blank">
                                            <i class="bi bi-file-earmark-pdf me-2 text-muted"></i> View Document
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    {{-- Add Payment --}}
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

                                    {{-- View Payments --}}
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
                        <td>{{ optional($purchase->date)->format('d-m-Y h:i A') }}</td>
                        <td><strong>{{ $purchase->reference_no }}</strong></td>
                        <td>{{ $purchase->location?->name ?? '—' }}</td>
                        <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $purchase->purchase_status }}">
                                {{ ucfirst($purchase->purchase_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $purchase->payment_status }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </td>
                        <td class="mono">৳ {{ number_format($purchase->grand_total, 2) }}</td>
                        <td class="mono">Purchase: ৳ {{ number_format($purchase->payment_due, 2) }}</td>
                        <td>{{ $purchase->addedBy?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No purchases found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer d-flex align-items-center justify-content-between py-2">
            <small class="text-muted">
                Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }} of {{ $purchases->total() }} entries
            </small>
            {{ $purchases->links('pagination::bootstrap-5') }}
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
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
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
                    {{-- Meta cards --}}
                    <div class="row g-2 mb-3" id="paymentMeta"></div>

                    <p class="text-danger small mb-3" id="advanceBalance"></p>

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
                                {{-- Populated via JS or eager loaded --}}
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
</div>
@endsection

@push('scripts')
<script>
/* ── Live search ── */
function liveSearch(q) {
    const lower = q.toLowerCase();
    document.querySelectorAll('#purchasesTable tbody tr').forEach(row => {
        const text = row.dataset.search || '';
        row.style.display = text.includes(lower) ? '' : 'none';
    });
}

/* ── Per page ── */
function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    window.location.href = url.toString();
}

/* ── VIEW MODAL ── */
document.getElementById('viewModal').addEventListener('show.bs.modal', function (e) {
    const url = e.relatedTarget.dataset.url;
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
            <td class="mono">৳ ${parseFloat(it.unit_cost_before_discount).toFixed(2)}</td>
            <td>${parseFloat(it.discount_percent).toFixed(2)}%</td>
            <td class="mono">৳ ${parseFloat(it.unit_cost_before_tax).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.subtotal_before_tax).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.tax_amount).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.unit_cost_after_tax).toFixed(2)}</td>
            <td class="mono">৳ ${parseFloat(it.subtotal).toFixed(2)}</td>
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
    const due        = Math.max(0, grandTotal - totalPaid);

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
                    <span class="badge badge-received">${p.purchase_status}</span>
                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">৳ ${grandTotal.toFixed(2)}</span>
                    <span class="badge badge-${p.payment_status}">${p.payment_status}</span>
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
    const form    = this;
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
                    <button class="btn btn-outline-primary btn-sm"
                        data-bs-dismiss="modal"
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
@endpush