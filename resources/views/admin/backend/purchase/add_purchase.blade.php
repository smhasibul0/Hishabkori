@extends('admin.admin_master')
@section('admin')

<style>
/* ═══════════════════════════════════════════════════
   PURCHASE FORM — Clean Admin Style
═══════════════════════════════════════════════════ */
:root {
    --pr:   #4f46e5;
    --pr-d: #3730a3;
    --pr-l: #eef2ff;
    --ok:   #059669;
    --ok-l: #dcfce7;
    --warn: #d97706;
    --warn-l: #fef3c7;
    --bad:  #dc2626;
    --bad-l:#fee2e2;
    --bdr:  #e2e8f0;
    --bg:   #f8fafc;
    --card: #ffffff;
    --tx:   #1e293b;
    --mu:   #64748b;
    --r:    8px;
    --sh:   0 1px 4px rgba(0,0,0,.07);
}
.pw { padding: 1.5rem; }
.page-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; }
.page-hdr h4 { font-size:1.1rem; font-weight:700; color:var(--tx); margin:0; display:flex; align-items:center; gap:.5rem; }
.page-hdr h4 i { color:var(--pr); }
.pc { background:var(--card); border:1px solid var(--bdr); border-radius:var(--r); box-shadow:var(--sh); margin-bottom:1.2rem; }
.pc-hdr { display:flex; align-items:center; gap:.6rem; padding:.65rem 1.2rem; border-bottom:1px solid var(--bdr); background:var(--bg); border-radius:var(--r) var(--r) 0 0; font-size:.72rem; font-weight:700; color:var(--mu); text-transform:uppercase; letter-spacing:.06em; }
.pc-hdr i { color:var(--pr); font-size:.85rem; }
.pc-body { padding:1.2rem; }
.flabel { display:block; font-size:.75rem; font-weight:600; color:var(--tx); margin-bottom:.32rem; }
.flabel .req { color:var(--bad); }
.flabel .tip { color:var(--mu); font-weight:400; font-size:.7rem; }
.finput, .fselect, .ftextarea { width:100%; border:1px solid var(--bdr); border-radius:6px; padding:.44rem .7rem; font-size:.81rem; color:var(--tx); background:#fff; outline:none; transition:border-color .18s, box-shadow .18s; font-family:inherit; }
.finput:focus, .fselect:focus, .ftextarea:focus { border-color:var(--pr); box-shadow:0 0 0 3px rgba(79,70,229,.11); }
.finput[readonly] { background:var(--bg); color:var(--mu); }
.ftextarea { resize:vertical; min-height:70px; }
.finput-prefix { display:flex; }
.finput-prefix .pfx { background:var(--bg); border:1px solid var(--bdr); border-right:none; border-radius:6px 0 0 6px; padding:.44rem .65rem; font-size:.8rem; color:var(--mu); display:flex; align-items:center; white-space:nowrap; }
.finput-prefix .finput { border-radius:0 6px 6px 0; }
/* Product search */
.psearch-wrap { position:relative; flex:1; }
.psearch-wrap .si { position:absolute; left:.65rem; top:50%; transform:translateY(-50%); color:var(--mu); font-size:.75rem; pointer-events:none; }
.psearch-wrap .finput { padding-left:2rem; }
.pdrop { position:absolute; top:calc(100% + 3px); left:0; right:0; background:#fff; border:1px solid var(--bdr); border-radius:6px; box-shadow:0 8px 28px rgba(0,0,0,.13); z-index:1000; max-height:250px; overflow-y:auto; display:none; }
.pdrop.open { display:block; }
.pdrop-item { padding:.5rem .85rem; font-size:.79rem; cursor:pointer; border-bottom:1px solid #f1f5f9; color:var(--tx); display:flex; justify-content:space-between; align-items:center; transition:background .1s; }
.pdrop-item:last-child { border-bottom:none; }
.pdrop-item:hover { background:var(--pr-l); }
.pdrop-item .pcode { font-size:.7rem; color:var(--mu); }
.pdrop-msg { padding:.9rem; text-align:center; color:var(--mu); font-size:.79rem; }
/* Products table */
.ptbl-wrap { overflow-x:auto; margin-top:.85rem; }
#productsTable { width:100%; border-collapse:collapse; font-size:.78rem; min-width:980px; }
#productsTable thead th { background:var(--pr); color:#fff; padding:.55rem .75rem; text-align:left; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
#productsTable tbody tr { border-bottom:1px solid var(--bdr); transition:background .1s; }
#productsTable tbody tr:nth-child(even) { background:#fafbfc; }
#productsTable tbody tr:hover { background:var(--pr-l); }
#productsTable td { padding:.45rem .75rem; vertical-align:middle; color:var(--tx); }
.tbl-in { border:1px solid var(--bdr); border-radius:5px; padding:.28rem .5rem; font-size:.78rem; width:100%; outline:none; background:#fff; transition:border-color .18s; font-family:inherit; }
.tbl-in:focus { border-color:var(--pr); box-shadow:0 0 0 2px rgba(79,70,229,.1); }
.tbl-sel { border:1px solid var(--bdr); border-radius:5px; padding:.28rem .4rem; font-size:.78rem; width:100%; outline:none; background:#fff; }
.pname-cell strong { font-size:.79rem; display:block; }
.pname-cell small { font-size:.68rem; color:var(--mu); }
.btn-rm { background:none; border:none; color:var(--bad); cursor:pointer; padding:.2rem .35rem; border-radius:4px; font-size:.9rem; transition:background .15s; }
.btn-rm:hover { background:var(--bad-l); }
/* Totals */
.tbar { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:1.5rem; padding:.65rem 1rem; background:var(--bg); border-top:2px solid var(--bdr); font-size:.8rem; }
.tbar-item { display:flex; gap:.4rem; align-items:center; }
.tbar-item .tl { color:var(--mu); }
.tbar-item .tv { font-weight:700; color:var(--tx); }
.tbar-item.big .tv { font-size:.95rem; color:var(--pr); }
/* Payment rows */
.pay-rows { display:flex; flex-direction:column; gap:.65rem; }
.pay-row { background:var(--bg); border:1px solid var(--bdr); border-radius:7px; padding:.85rem 1rem .75rem; position:relative; }
.pay-row-grid { display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:.7rem; align-items:end; }
.pay-num-badge { position:absolute; top:-.55rem; left:.9rem; background:var(--pr); color:#fff; font-size:.65rem; font-weight:700; padding:.1rem .55rem; border-radius:99px; letter-spacing:.03em; }
.btn-add-pay { display:inline-flex; align-items:center; gap:.4rem; background:var(--ok-l); color:var(--ok); border:1px dashed var(--ok); border-radius:6px; padding:.42rem .9rem; font-size:.78rem; font-weight:600; cursor:pointer; transition:all .18s; margin-top:.65rem; }
.btn-add-pay:hover { background:var(--ok); color:#fff; }
.btn-rm-pay { background:none; border:none; color:var(--bad); cursor:pointer; padding:.35rem .45rem; border-radius:5px; font-size:1rem; transition:background .15s; }
.btn-rm-pay:hover { background:var(--bad-l); }
/* Payment summary */
.pay-summary { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:1.5rem; padding:.8rem 1rem; background:var(--bg); border-top:2px solid var(--bdr); align-items:center; margin-top:.75rem; border-radius:0 0 var(--r) var(--r); }
.ps-stat { display:flex; gap:.4rem; align-items:center; font-size:.8rem; }
.ps-stat .sl { color:var(--mu); }
.ps-stat .sv { font-weight:700; color:var(--tx); }
.ps-stat .sv.due  { color:var(--bad); }
.ps-stat .sv.part { color:var(--warn); }
.ps-stat .sv.paid { color:var(--ok); }
.sbadge { display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .7rem; border-radius:99px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
.sb-due  { background:var(--bad-l);  color:var(--bad); }
.sb-part { background:var(--warn-l); color:var(--warn); }
.sb-paid { background:var(--ok-l);   color:var(--ok); }
/* Expenses */
.exp-table { width:100%; border-collapse:collapse; font-size:.79rem; margin-top:.75rem; }
.exp-table th { background:var(--bg); border:1px solid var(--bdr); padding:.4rem .65rem; font-size:.7rem; font-weight:600; color:var(--mu); text-align:left; }
.exp-table td { border:1px solid var(--bdr); padding:.35rem .65rem; }
.exp-panel { display:none; }
.exp-panel.open { display:block; }
/* Buttons */
.btn-pr { background:var(--pr); color:#fff; border:none; border-radius:6px; padding:.48rem 1.2rem; font-size:.81rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:background .18s, transform .1s; }
.btn-pr:hover { background:var(--pr-d); }
.btn-pr:active { transform:scale(.97); }
.btn-outline { background:#fff; color:var(--pr); border:1px solid var(--pr); border-radius:6px; padding:.48rem 1.1rem; font-size:.81rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:all .18s; text-decoration:none; }
.btn-outline:hover { background:var(--pr-l); color:var(--pr); }
.btn-sm { padding:.3rem .65rem; font-size:.74rem; }
.btn-import { background:var(--pr); color:#fff; border:none; border-radius:6px; padding:.4rem .9rem; font-size:.78rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; transition:background .18s; }
.btn-import:hover { background:var(--pr-d); }
.btn-toggle-exp { background:var(--pr-l); color:var(--pr); border:1px solid #c7d2fe; border-radius:6px; padding:.4rem .9rem; font-size:.78rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; transition:all .18s; }
.btn-toggle-exp:hover { background:var(--pr); color:#fff; }
.act-row { display:flex; justify-content:center; gap:.85rem; padding:1rem 0 .5rem; }
.alert-err { background:var(--bad-l); border:1px solid #fca5a5; border-radius:6px; padding:.65rem 1rem; font-size:.8rem; color:var(--bad); margin-bottom:1rem; display:none; }
.alert-err.show { display:block; }
/* Grid helpers */
.g2 { display:grid; grid-template-columns:repeat(2,1fr); gap:.85rem; }
.g3 { display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; }
.g4 { display:grid; grid-template-columns:repeat(4,1fr); gap:.85rem; }
@media(max-width:900px){ .g4 { grid-template-columns:1fr 1fr; } .pay-row-grid { grid-template-columns:1fr 1fr; } }
@media(max-width:600px){ .g3,.g4,.g2 { grid-template-columns:1fr; } .pay-row-grid { grid-template-columns:1fr; } }
</style>

<div class="pw">
    <div class="page-hdr">
        <h4><i class="fas fa-shopping-cart"></i> Add Purchase</h4>
        <a href="{{ route('all.purchase') }}" class="btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    @if($errors->any())
        <div class="alert-err show"><i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}</div>
    @endif
    <div class="alert-err" id="jsError"></div>

    <form id="purchaseForm" action="{{ route('store.purchase') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ══ SECTION 1: Purchase Info ══ --}}
        <div class="pc">
            <div class="pc-hdr"><i class="fas fa-info-circle"></i> Purchase Information</div>
            <div class="pc-body">
                <div class="g4" style="margin-bottom:.85rem;">
                    <div>
                        <label class="flabel">Supplier <span class="req">*</span></label>
                        <select name="supplier_id" id="supplierSel" class="fselect" required>
                            <option value="">Please Select</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                    data-address="{{ $supplier->address }}"
                                    data-advance="{{ $supplier->advance_balance ?? 0 }}">
                                    
                                {{ $supplier->business_name ? $supplier->business_name : $supplier->name }}

                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flabel">Reference No <span class="tip">(auto if blank)</span></label>
                        <input type="text" name="reference_no" class="finput" placeholder="PUR-XXXXXX" value="{{ old('reference_no') }}">
                    </div>
                    <div>
                        <label class="flabel">Purchase Date <span class="req">*</span></label>
                        <input type="datetime-local" name="purchase_date" class="finput"
                               value="{{ old('purchase_date', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div>
                        <label class="flabel">Purchase Status <span class="req">*</span></label>
                        <select name="purchase_status" class="fselect" required>
                            <option value="">Please Select</option>
                            <option value="received" {{ old('purchase_status')=='received'?'selected':'' }}>Received</option>
                            <option value="pending"  {{ old('purchase_status')=='pending' ?'selected':'' }}>Pending</option>
                            <option value="ordered"  {{ old('purchase_status')=='ordered' ?'selected':'' }}>Ordered</option>
                        </select>
                    </div>
                </div>
                <div class="g4">
                    <div>
                        <label class="flabel">Supplier Address</label>
                        <input type="text" id="supplierAddr" class="finput" readonly placeholder="Select supplier…">
                    </div>
                    <div>
                        <label class="flabel">Business Location</label>
                        <select name="business_location_id" class="fselect">
                            <option value="">Please Select</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('business_location_id')==$loc->id?'selected':'' }}>
                                    {{ $loc->name }} ({{ $loc->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flabel">Pay Term</label>
                        <div style="display:flex;gap:.4rem;">
                            <input type="number" name="pay_term_number" class="finput" placeholder="e.g. 30" min="0" value="{{ old('pay_term_number') }}" style="width:60%;">
                            <select name="pay_term_type" class="fselect" style="width:40%;">
                                <option value="">—</option>
                                <option value="days"   {{ old('pay_term_type')=='days'  ?'selected':'' }}>Days</option>
                                <option value="months" {{ old('pay_term_type')=='months'?'selected':'' }}>Months</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="flabel">Attach Document</label>
                        <input type="file" name="document" class="finput" accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png">
                        <small style="font-size:.68rem;color:var(--mu);">Max 5MB · pdf,csv,zip,doc,docx,jpg,png</small>
                    </div>
                </div>
                <div style="margin-top:.85rem;">
                    <label class="flabel">Additional Notes</label>
                    <textarea name="additional_notes" class="ftextarea" rows="2" placeholder="Any notes about this purchase…">{{ old('additional_notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 2: Products ══ --}}
        <div class="pc">
            <div class="pc-hdr"><i class="fas fa-boxes"></i> Products</div>
            <div class="pc-body">
                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
                    <button type="button" class="btn-import"><i class="fas fa-file-import"></i> Import Products</button>
                    <div class="psearch-wrap" style="max-width:520px;flex:1;">
                        <i class="fas fa-search si"></i>
                        <input type="text" id="productSearch" class="finput"
                               placeholder="Enter Product name / SKU / Scan barcode" autocomplete="off">
                        <div class="pdrop" id="productDrop"></div>
                    </div>
                    <a href="{{ route('add.product') }}" class="btn-outline btn-sm" style="margin-left:auto;">
                        <i class="fas fa-plus"></i> Add new product
                    </a>
                </div>

                <div class="ptbl-wrap">
                    <table id="productsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th style="width:90px;">Qty</th>
                                <th style="width:80px;">Unit</th>
                                <th style="width:125px;">Unit Cost</th>
                                <th style="width:100px;">Disc %</th>
                                <th style="width:130px;">Cost (After Disc)</th>
                                <th style="width:110px;">Line Total</th>
                                <th style="width:95px;">Margin %</th>
                                <th style="width:130px;">Selling Price</th>
                                <th style="width:36px;"></th>
                            </tr>
                        </thead>
                        <tbody id="prodTbody">
                            <tr id="emptyRow">
                                <td colspan="11" style="text-align:center;padding:2.5rem 1rem;color:var(--mu);font-size:.8rem;">
                                    <i class="fas fa-box-open" style="font-size:1.8rem;display:block;margin-bottom:.5rem;opacity:.35;"></i>
                                    No products yet — search above to add
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="g3" style="margin-top:1rem;">
                    <div>
                        <label class="flabel">Discount Type</label>
                        <select id="discType" name="discount_type" class="fselect">
                            <option value="none">None</option>
                            <option value="fixed">Fixed (৳)</option>
                            <option value="percent">Percentage (%)</option>
                        </select>
                    </div>
                    <div id="discValWrap" style="display:none;">
                        <label class="flabel">Discount Value</label>
                        <input type="number" id="discVal" name="discount_value" class="finput" value="0" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="flabel">Purchase Tax</label>
                        <select id="taxSel" name="purchase_tax" class="fselect">
                            <option value="0">None</option>
                            @foreach($taxes as $t)
                                <option value="{{ $t->rate }}">{{ $t->name }} ({{ $t->rate }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="tbar" style="margin-top:.85rem;">
                    <div class="tbar-item"><span class="tl">Items:</span><span class="tv" id="ttlItems">0</span></div>
                    <div class="tbar-item"><span class="tl">Subtotal:</span><span class="tv">৳ <span id="ttlSub">0.00</span></span></div>
                    <div class="tbar-item"><span class="tl">Discount:</span><span class="tv">— ৳ <span id="ttlDisc">0.00</span></span></div>
                    <div class="tbar-item"><span class="tl">Tax (+):</span><span class="tv">৳ <span id="ttlTax">0.00</span></span></div>
                    <div class="tbar-item big"><span class="tl">Net Total:</span><span class="tv">৳ <span id="ttlNet">0.00</span></span></div>
                </div>

                <input type="hidden" name="products_json" id="productsJson">
            </div>
        </div>

        {{-- ══ SECTION 3: Shipping ══ --}}
        <div class="pc">
            <div class="pc-hdr"><i class="fas fa-truck"></i> Shipping & Additional Charges</div>
            <div class="pc-body">
                <div class="g3" style="align-items:end;">
                    <div>
                        <label class="flabel">Shipping Details</label>
                        <input type="text" name="shipping_details" class="finput" placeholder="Carrier / tracking number…">
                    </div>
                    <div>
                        <label class="flabel">(+) Shipping Charges</label>
                        <div class="finput-prefix">
                            <span class="pfx">৳</span>
                            <input type="number" id="shipCharge" name="shipping_charges" class="finput" value="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn-toggle-exp" id="toggleExp">
                            <i class="fas fa-plus"></i> Additional expenses <i class="fas fa-chevron-down" id="expIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="exp-panel" id="expPanel">
                    <table class="exp-table">
                        <thead><tr><th style="width:60%;">Expense Name</th><th>Amount (৳)</th></tr></thead>
                        <tbody>
                            @for($i=0;$i<4;$i++)
                            <tr>
                                <td><input type="text" name="expense_name[]" class="tbl-in" placeholder="e.g. Port handling fee…"></td>
                                <td><input type="number" name="expense_amount[]" class="tbl-in exp-amt" value="0" min="0" step="0.01"></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="tbar" style="margin-top:.85rem;">
                    <div class="tbar-item big">
                        <span class="tl">Purchase Total (inc. shipping & expenses):</span>
                        <span class="tv">৳ <span id="purTotal">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SECTION 4: Multiple Payments ══ --}}
        <div class="pc">
            <div class="pc-hdr"><i class="fas fa-money-bill-wave"></i> Payments</div>
            <div class="pc-body">

                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;margin-bottom:.85rem;">
                    <small style="color:var(--mu);font-size:.77rem;">
                        Supplier Advance Balance: <strong id="advBal">৳ 0.00</strong>
                        &nbsp;·&nbsp; You can split payment across multiple methods below.
                    </small>
                </div>

                {{-- Payment rows --}}
                <div class="pay-rows" id="payRowsWrap">
                    <div class="pay-row" id="payRow_1">
                        <span class="pay-num-badge">Payment 1</span>
                        <div class="pay-row-grid">
                            <div>
                                <label class="flabel">Amount <span class="req">*</span></label>
                                <div class="finput-prefix">
                                    <span class="pfx">৳</span>
                                    <input type="number" name="payment_amount[]" class="finput pay-amount" value="0.00" min="0" step="0.01">
                                </div>
                            </div>
                            <div>
                                <label class="flabel">Paid On <span class="req">*</span></label>
                                <input type="datetime-local" name="paid_on[]" class="finput" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div>
                                <label class="flabel">Payment Method <span class="req">*</span></label>
                                <select name="payment_method[]" class="fselect pay-method">
                                    <option value="">Please Select</option>
                                    @foreach($paymentMethods as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="flabel">Payment Account</label>
                                <select name="payment_account[]" class="fselect pay-account">
                                    <option value="">— Select Method First —</option>
                                </select>
                            </div>
                            <div>
                                {{-- No remove on row 1 --}}
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-pay" id="addPayBtn">
                    <i class="fas fa-plus"></i> Add another payment method
                </button>

                <div style="margin-top:.85rem;">
                    <label class="flabel">Payment Note <span class="tip">(applies to all payments)</span></label>
                    <textarea name="payment_note" class="ftextarea" rows="2" placeholder="Optional note about these payments…"></textarea>
                </div>

                {{-- Payment summary --}}
                <div class="pay-summary">
                    <div class="ps-stat">
                        <span class="sl">Purchase Total:</span>
                        <span class="sv">৳ <span id="ps_total">0.00</span></span>
                    </div>
                    <div class="ps-stat">
                        <span class="sl">Total Paying:</span>
                        <span class="sv">৳ <span id="ps_paying">0.00</span></span>
                    </div>
                    <div class="ps-stat">
                        <span class="sl">Balance Due:</span>
                        <span class="sv due" id="ps_due">৳ 0.00</span>
                    </div>
                    <div class="ps-stat">
                        <span class="sl">Status:</span>
                        <span id="ps_badge" class="sbadge sb-due">
                            <i class="fas fa-circle" style="font-size:.45rem;"></i> Due
                        </span>
                    </div>
                </div>

                <input type="hidden" name="payment_status" id="payStatus" value="due">
            </div>
        </div>

        {{-- Actions --}}
        <div class="act-row">
            <button type="submit" class="btn-pr" style="padding:.55rem 2.4rem;font-size:.88rem;">
                <i class="fas fa-save"></i> Save Purchase
            </button>
            <a href="{{ route('all.purchase') }}" class="btn-outline" style="padding:.55rem 1.6rem;font-size:.88rem;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Helpers ── */
    const fmt  = v => parseFloat(v || 0).toFixed(2);
    const fmtN = v => parseFloat(v || 0);
    const $    = id => document.getElementById(id);

    function showError(msg) {
        const el = $('jsError');
        el.textContent = msg;
        el.classList.add('show');
        el.scrollIntoView({ behavior:'smooth', block:'center' });
        setTimeout(() => el.classList.remove('show'), 5000);
    }

    /* ── 1. Supplier → address & advance ── */
    $('supplierSel').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        $('supplierAddr').value  = opt.dataset.address  || '';
        $('advBal').textContent  = '৳ ' + fmt(opt.dataset.advance || 0);
    });

    /* ── 2. Product Search ── */
    const pSearch = $('productSearch');
    const pDrop   = $('productDrop');
    let timer;

    pSearch.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);
        if (q.length < 2) { pDrop.classList.remove('open'); return; }
        pDrop.innerHTML = '<div class="pdrop-msg"><i class="fas fa-spinner fa-spin"></i> Searching…</div>';
        pDrop.classList.add('open');
        timer = setTimeout(() => {
            fetch(`/purchase/search-products?q=${encodeURIComponent(q)}`, { headers:{'X-Requested-With':'XMLHttpRequest'} })
            .then(r => r.json())
            .then(data => {
                pDrop.innerHTML = '';
                if (!data.products?.length) {
                    pDrop.innerHTML = '<div class="pdrop-msg">No products found</div>';
                    return;
                }
                data.products.forEach(p => {
                    const d = document.createElement('div');
                    d.className = 'pdrop-item';
                    d.innerHTML = `<span>${p.product_name}</span><span class="pcode">${p.product_code} &middot; Stock: ${p.quantity ?? 0}</span>`;
                    d.addEventListener('click', () => addProduct(p));
                    pDrop.appendChild(d);
                });
            })
            .catch(() => { pDrop.innerHTML = '<div class="pdrop-msg">Error loading results</div>'; });
        }, 280);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.psearch-wrap')) pDrop.classList.remove('open');
    });

    /* ── 3. Product Table ── */
    let rows = [], rowIdx = 0;

    function addProduct(p) {
        pDrop.classList.remove('open');
        pSearch.value = '';
        const existing = rows.find(r => r.pid === p.id);
        if (existing) {
            document.querySelector(`[data-qty="${existing.rid}"]`)?.focus();
            return;
        }
        rowIdx++;
        const rid = rowIdx;
        const row = {
            rid, pid: p.id,
            name: p.product_name, code: p.product_code, stock: p.quantity ?? 0,
            qty: 1, unit: 'Pieces',
            unitCost: fmtN(p.purchase_price), discPct: 0,
            margin: 0, sellingPrice: fmtN(p.selling_price),
            _aftDisc: fmtN(p.purchase_price), _lineTot: fmtN(p.purchase_price),
        };
        rows.push(row);
        const empty = $('emptyRow');
        if (empty) empty.remove();
        const tbody = $('prodTbody');
        const tr = document.createElement('tr');
        tr.id = `pr_${rid}`;
        tr.innerHTML = buildRow(row, rows.length);
        tbody.appendChild(tr);
        bindRow(tr, row);
        calcRow(row);
        calcTotals();
    }

    function buildRow(r, num) {
        const units = ['Pieces','Box','Set','Kg','Litre','Dozen']
            .map(u => `<option ${u===r.unit?'selected':''}>${u}</option>`).join('');
        return `
        <td>${num}</td>
        <td class="pname-cell"><strong>${r.name}</strong><small>Code: ${r.code} &nbsp;|&nbsp; Stock: ${r.stock}</small></td>
        <td><input type="number" class="tbl-in" data-qty="${r.rid}" value="1" min="0.01" step="any"></td>
        <td><select class="tbl-sel" data-unit="${r.rid}">${units}</select></td>
        <td>
            <input type="number" class="tbl-in" data-cost="${r.rid}" value="${r.unitCost.toFixed(2)}" min="0" step="0.01">
            <small style="font-size:.66rem;color:var(--mu);">Prev: ৳${r.unitCost.toFixed(2)}</small>
        </td>
        <td>
            <input type="number" class="tbl-in" data-disc="${r.rid}" value="0" min="0" max="100" step="0.01">
            <small style="font-size:.66rem;color:var(--mu);">Prev: 0.00%</small>
        </td>
        <td class="cad-${r.rid}" style="font-weight:600;">${r.unitCost.toFixed(2)}</td>
        <td class="clt-${r.rid}" style="font-weight:700;color:var(--pr);">${r.unitCost.toFixed(2)}</td>
        <td><input type="number" class="tbl-in" data-margin="${r.rid}" value="0" min="0" step="0.01"></td>
        <td><input type="number" class="tbl-in" data-sell="${r.rid}" value="${r.sellingPrice.toFixed(2)}" min="0" step="0.01"></td>
        <td><button type="button" class="btn-rm" data-rmrow="${r.rid}"><i class="fas fa-times"></i></button></td>`;
    }

    function bindRow(tr, row) {
        const g = attr => tr.querySelector(`[data-${attr}="${row.rid}"]`);
        g('qty').addEventListener('input',    e => { row.qty      = fmtN(e.target.value); calcRow(row); calcTotals(); });
        g('cost').addEventListener('input',   e => { row.unitCost = fmtN(e.target.value); calcRow(row); calcTotals(); });
        g('disc').addEventListener('input',   e => { row.discPct  = fmtN(e.target.value); calcRow(row); calcTotals(); });
        g('margin').addEventListener('input', e => { row.margin   = fmtN(e.target.value); });
        g('sell').addEventListener('input',   e => { row.sellingPrice = fmtN(e.target.value); });
        g('unit').addEventListener('change',  e => { row.unit     = e.target.value; });
        g('rmrow').addEventListener('click', () => {
            rows = rows.filter(r => r.rid !== row.rid);
            tr.remove();
            document.querySelectorAll('#prodTbody tr:not(#emptyRow)').forEach((r, i) => {
                r.querySelector('td:first-child').textContent = i + 1;
            });
            if (!rows.length) {
                $('prodTbody').innerHTML = `<tr id="emptyRow"><td colspan="11" style="text-align:center;padding:2.5rem 1rem;color:var(--mu);font-size:.8rem;"><i class="fas fa-box-open" style="font-size:1.8rem;display:block;margin-bottom:.5rem;opacity:.35;"></i>No products yet — search above to add</td></tr>`;
            }
            calcTotals();
        });
    }

    function calcRow(row) {
        const ad = row.unitCost * (1 - row.discPct / 100);
        const lt = ad * row.qty;
        row._aftDisc = ad; row._lineTot = lt;
        document.querySelector(`.cad-${row.rid}`).textContent = ad.toFixed(2);
        document.querySelector(`.clt-${row.rid}`).textContent = lt.toFixed(2);
    }

    /* ── 4. Totals ── */
    function calcTotals() {
        const subtotal = rows.reduce((s, r) => s + r.unitCost * r.qty, 0);
        let discAmt = 0;
        const dType = $('discType').value, dVal = fmtN($('discVal')?.value);
        if (dType === 'fixed')   discAmt = dVal;
        if (dType === 'percent') discAmt = subtotal * dVal / 100;
        const afterDisc = subtotal - discAmt;
        const taxRate   = fmtN($('taxSel').value);
        const taxAmt    = afterDisc * taxRate / 100;
        const netTotal  = afterDisc + taxAmt;
        const shipping  = fmtN($('shipCharge').value);
        let extras = 0;
        document.querySelectorAll('.exp-amt').forEach(i => extras += fmtN(i.value));
        const purTotal  = netTotal + shipping + extras;

        $('ttlItems').textContent = rows.length;
        $('ttlSub').textContent   = fmt(subtotal);
        $('ttlDisc').textContent  = fmt(discAmt);
        $('ttlTax').textContent   = fmt(taxAmt);
        $('ttlNet').textContent   = fmt(netTotal);
        $('purTotal').textContent = fmt(purTotal);
        $('ps_total').textContent = fmt(purTotal);

        updatePayStatus(purTotal);
        saveJson();
    }

    /* ── 5. Payment Status (multi-payment) ── */
    function totalPaying() {
        let s = 0;
        document.querySelectorAll('.pay-amount').forEach(i => s += fmtN(i.value));
        return s;
    }

    function updatePayStatus(purTotal) {
        if (purTotal === undefined) purTotal = fmtN($('purTotal').textContent);
        const paying = totalPaying();
        const due    = Math.max(0, purTotal - paying);

        $('ps_paying').textContent = fmt(paying);
        $('ps_due').textContent    = '৳ ' + fmt(due);

        const badge = $('ps_badge');
        badge.classList.remove('sb-due','sb-part','sb-paid');
        $('ps_due').classList.remove('due','part','paid');

        let status;
        if (paying <= 0) {
            status = 'due';     badge.classList.add('sb-due');  $('ps_due').classList.add('due');
            badge.innerHTML = '<i class="fas fa-circle" style="font-size:.45rem;"></i> Due';
        } else if (paying < purTotal) {
            status = 'partial'; badge.classList.add('sb-part'); $('ps_due').classList.add('part');
            badge.innerHTML = '<i class="fas fa-circle" style="font-size:.45rem;"></i> Partial';
        } else {
            status = 'paid';    badge.classList.add('sb-paid'); $('ps_due').classList.add('paid');
            badge.innerHTML = '<i class="fas fa-circle" style="font-size:.45rem;"></i> Paid';
        }
        $('payStatus').value = status;
    }

    document.addEventListener('input', e => {
        if (e.target.classList.contains('pay-amount')) updatePayStatus();
    });

    /* ── 6. Payment Accounts AJAX ── */
    function loadAccounts(methodSel, accountSel) {
        const id = methodSel.value;
        accountSel.innerHTML = '<option value="">Loading…</option>';
        if (!id) { accountSel.innerHTML = '<option value="">— Select Method First —</option>'; return; }
        fetch(`/purchase/payment-accounts?method_id=${id}`, { headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(r => r.json())
        .then(data => {
            accountSel.innerHTML = '<option value="">Please Select</option>';
            if (data.accounts?.length) {
                data.accounts.forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.id;
                    o.textContent = a.name + (a.account_number ? ` (${a.account_number})` : '');
                    accountSel.appendChild(o);
                });
            } else {
                accountSel.innerHTML = '<option value="">No accounts for this method</option>';
            }
        })
        .catch(() => { accountSel.innerHTML = '<option value="">Error loading accounts</option>'; });
    }

    function bindPayRow(rowEl) {
        const m = rowEl.querySelector('.pay-method');
        const a = rowEl.querySelector('.pay-account');
        if (m && a) m.addEventListener('change', () => loadAccounts(m, a));
    }

    bindPayRow(document.getElementById('payRow_1'));

    /* ── 7. Add / Remove Payment Rows ── */
    let payCount = 1;

    $('addPayBtn').addEventListener('click', function () {
        payCount++;
        const n = payCount;

        // Clone method options from row 1
        const methodOptions = '<option value="">Please Select</option>' +
            Array.from(document.querySelector('#payRow_1 .pay-method').options)
                .filter(o => o.value)
                .map(o => `<option value="${o.value}">${o.textContent}</option>`)
                .join('');

        const div = document.createElement('div');
        div.className = 'pay-row';
        div.id = `payRow_${n}`;
        div.innerHTML = `
            <span class="pay-num-badge">Payment ${n}</span>
            <div class="pay-row-grid">
                <div>
                    <label class="flabel">Amount <span class="req">*</span></label>
                    <div class="finput-prefix">
                        <span class="pfx">৳</span>
                        <input type="number" name="payment_amount[]" class="finput pay-amount" value="0.00" min="0" step="0.01">
                    </div>
                </div>
                <div>
                    <label class="flabel">Paid On <span class="req">*</span></label>
                    <input type="datetime-local" name="paid_on[]" class="finput" value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
                <div>
                    <label class="flabel">Payment Method <span class="req">*</span></label>
                    <select name="payment_method[]" class="fselect pay-method">${methodOptions}</select>
                </div>
                <div>
                    <label class="flabel">Payment Account</label>
                    <select name="payment_account[]" class="fselect pay-account">
                        <option value="">— Select Method First —</option>
                    </select>
                </div>
                <div style="display:flex;align-items:flex-end;padding-bottom:.05rem;">
                    <button type="button" class="btn-rm-pay" data-rm="${n}" title="Remove payment">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>`;

        $('payRowsWrap').appendChild(div);
        bindPayRow(div);

        div.querySelector(`[data-rm="${n}"]`).addEventListener('click', function () {
            div.remove();
            // Re-label badges
            document.querySelectorAll('.pay-num-badge').forEach((b, i) => {
                b.textContent = `Payment ${i + 1}`;
            });
            updatePayStatus();
        });

        div.scrollIntoView({ behavior:'smooth', block:'nearest' });
    });

    /* ── 8. Discount / Tax / Shipping / Expenses ── */
    $('discType').addEventListener('change', function () {
        $('discValWrap').style.display = this.value !== 'none' ? 'block' : 'none';
        calcTotals();
    });
    document.addEventListener('input', e => {
        if (['discVal','shipCharge'].includes(e.target.id)) calcTotals();
        if (e.target.classList.contains('exp-amt')) calcTotals();
    });
    $('taxSel').addEventListener('change', calcTotals);

    /* ── 9. Extra expenses toggle ── */
    $('toggleExp').addEventListener('click', function () {
        const open = $('expPanel').classList.toggle('open');
        $('expIcon').className = open ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
    });

    /* ── 10. Save products JSON ── */
    function saveJson() {
        $('productsJson').value = JSON.stringify(rows.map(r => ({
            product_id:               r.pid,
            product_name:             r.name,
            qty:                      r.qty,
            unit:                     r.unit,
            unit_cost:                r.unitCost,
            discount_percent:         r.discPct,
            unit_cost_after_discount: +r._aftDisc.toFixed(2),
            line_total:               +r._lineTot.toFixed(2),
            profit_margin:            r.margin,
            selling_price:            r.sellingPrice,
        })));
    }

    /* ── 11. Form submit validation ── */
    $('purchaseForm').addEventListener('submit', function (e) {
        if (!rows.length) {
            e.preventDefault();
            showError('Please add at least one product before saving.');
            return;
        }
        let valid = true;
        document.querySelectorAll('.pay-row').forEach(row => {
            const amt = fmtN(row.querySelector('.pay-amount')?.value);
            const mth = row.querySelector('.pay-method')?.value;
            if (amt > 0 && !mth) {
                valid = false;
                row.querySelector('.pay-method').style.borderColor = 'var(--bad)';
            } else if (row.querySelector('.pay-method')) {
                row.querySelector('.pay-method').style.borderColor = '';
            }
        });
        if (!valid) {
            e.preventDefault();
            showError('Please select a payment method for every payment row that has an amount.');
            return;
        }
        saveJson();
    });

    calcTotals();
});
</script>

@endsection