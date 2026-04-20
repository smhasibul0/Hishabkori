<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseAdditionalExpense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\AccountType;
use App\Models\PaymentAccount;
use App\Models\WareHouse;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // All Purchases List
    // ──────────────────────────────────────────────────────────
    public function allPurchase()
    {
        $purchases = Purchase::with(['supplier', 'location', 'payments', 'items.product', 'addedBy'])
            ->latest()
            ->get();

        return view('admin.backend.purchase.all_purchase', compact('purchases'));
    }

    // ──────────────────────────────────────────────────────────
    // Add Purchase Form
    // ──────────────────────────────────────────────────────────
    public function addPurchase()
    {
        $suppliers      = Supplier::orderBy('name')->get();
        $paymentMethods = AccountType::orderBy('name')->get();
        $locations      = WareHouse::orderBy('name')->get();
        $taxes          = Tax::orderBy('name')->get();

        return view('admin.backend.purchase.add_purchase', compact(
            'suppliers', 'paymentMethods', 'locations', 'taxes'
        ));
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Search Products
    // ──────────────────────────────────────────────────────────
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::where('product_name', 'like', '%' . $query . '%')
                        ->orWhere('product_code', 'like', '%' . $query . '%')
                        ->select('id', 'product_name', 'product_code', 'quantity', 'purchase_price', 'selling_price')
                        ->limit(15)
                        ->get();

        return response()->json(['products' => $products]);
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Payment Accounts by Method
    // ──────────────────────────────────────────────────────────
    public function getPaymentAccounts(Request $request)
    {
        $methodId = $request->get('method_id');

        if (!$methodId) {
            return response()->json(['accounts' => []]);
        }

        $accounts = PaymentAccount::where('payment_method_id', $methodId)
                                ->where('is_active', true)
                                ->select('id', 'name', 'account_number')
                                ->get();

        return response()->json(['accounts' => $accounts]);
    }

    // ──────────────────────────────────────────────────────────
    // Store Purchase
    // ──────────────────────────────────────────────────────────
    public function storePurchase(Request $request)
    {
        $request->validate([
            'supplier_id'       => 'required|exists:suppliers,id',
            'purchase_date'     => 'required',
            'purchase_status'   => 'required|in:received,pending,ordered',
            'products_json'     => 'required|json',
            'payment_amount'    => 'nullable|array',
            'payment_amount.*'  => 'nullable|numeric|min:0',
            'paid_on'           => 'nullable|array',
            'payment_method'    => 'nullable|array',
            'payment_account'   => 'nullable|array',
            'document'          => 'nullable|file|max:5120|mimes:pdf,csv,zip,doc,docx,jpeg,jpg,png',
        ]);

        $products = json_decode($request->products_json, true);

        if (empty($products)) {
            return back()
                ->withErrors(['products_json' => 'Please add at least one product.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // ── 1. Calculate totals ─────────────────────────────────

            $subtotal = array_sum(array_column($products, 'line_total'));

            $discountType   = $request->discount_type ?? 'none';
            $discountValue  = (float) ($request->discount_value ?? 0);
            $discountAmount = 0;
            if ($discountType === 'fixed')   $discountAmount = $discountValue;
            if ($discountType === 'percent') $discountAmount = $subtotal * ($discountValue / 100);

            $afterDiscount = $subtotal - $discountAmount;

            $taxRate   = (float) ($request->purchase_tax ?? 0);
            $taxAmount = $afterDiscount * ($taxRate / 100);
            $netTotal  = $afterDiscount + $taxAmount;

            $shippingCharges = (float) ($request->shipping_charges ?? 0);
            $extraExpenses   = 0;
            $expenseNames    = $request->expense_name   ?? [];
            $expenseAmounts  = $request->expense_amount ?? [];
            foreach ($expenseAmounts as $a) $extraExpenses += (float) $a;

            // grand_total = everything included
            $grandTotal = $netTotal + $shippingCharges + $extraExpenses;

            // ── 2. Total paid ───────────────────────────────────────

            $paymentAmounts  = $request->payment_amount  ?? [];
            $paymentDates    = $request->paid_on          ?? [];
            $paymentMethods  = $request->payment_method   ?? [];
            $paymentAccounts = $request->payment_account  ?? [];

            $totalPaid = 0;
            foreach ($paymentAmounts as $i => $amt) {
                $amt = (float) $amt;
                if ($amt > 0 && !empty($paymentMethods[$i])) {
                    $totalPaid += $amt;
                }
            }

            $amountDue = max(0, $grandTotal - $totalPaid);

            if ($totalPaid <= 0) {
                $paymentStatus = 'due';
            } elseif ($totalPaid < $grandTotal) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'paid';
            }

            // ── 3. Document upload ──────────────────────────────────

            $documentPath = null;
            if ($request->hasFile('document')) {
                $file         = $request->file('document');
                $filename     = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/purchase_documents'), $filename);
                $documentPath = 'upload/purchase_documents/' . $filename;
            }

            // ── 4. Reference number ─────────────────────────────────

            $referenceNo = $request->reference_no
                ?: 'PUR-' . strtoupper(uniqid());

            // ── 5. Create Purchase record ───────────────────────────
            //
            //  FIX: use 'date' (matches model cast + accessor),
            //       'grand_total' (matches model cast + getPaymentDueAttribute),
            //       'business_location_id' (matches location() FK),
            //       'created_by' (matches addedBy() FK).
            //

            $purchase = Purchase::create([
                'supplier_id'          => $request->supplier_id,
                'business_location_id' => $request->business_location_id ?: null,
                'created_by'           => auth()->id(),
                'reference_no'         => $referenceNo,

                'purchase_date'        => Carbon::parse($request->purchase_date),
                'purchase_status'      => $request->purchase_status,
                'pay_term_number'      => $request->pay_term_number ?: null,
                'pay_term_type'        => $request->pay_term_type   ?: null,
                'discount_type'        => $discountType,
                'discount_value'       => $discountValue,
                'discount_amount'      => round($discountAmount, 2),
                'purchase_tax_rate'    => $taxRate,
                'tax_amount'           => round($taxAmount, 2),
                'shipping_details'     => $request->shipping_details ?: null,
                'shipping_charges'     => round($shippingCharges, 2),
                'subtotal'             => round($subtotal, 2),
                'net_total'            => round($netTotal, 2),
                'purchase_total'       => round($grandTotal, 2),
                'amount_paid'          => round($totalPaid, 2),
                'amount_due'           => round($amountDue, 2),
                'payment_status'       => $paymentStatus,
                'additional_notes'     => $request->additional_notes ?: null,
                'document'             => $documentPath,
            ]);

            // ── 6. Save line items + update stock ───────────────────

            foreach ($products as $item) {
                $unitCost        = (float) $item['unit_cost'];
                $discPct         = (float) ($item['discount_percent'] ?? 0);
                $unitCostAftDisc = $unitCost * (1 - $discPct / 100);
                $qty             = (float) $item['qty'];
                $lineTotal       = round($unitCostAftDisc * $qty, 2);

                PurchaseItem::create([
                    'purchase_id'              => $purchase->id,
                    'product_id'               => $item['product_id'],
                    'qty'                      => $qty,
                    'unit'                     => $item['unit']          ?? 'Pieces',
                    'unit_cost'                => $unitCost,
                    'discount_percent'         => $discPct,
                    'unit_cost_after_discount' => round($unitCostAftDisc, 2),
                    'line_total'               => $lineTotal,
                    'profit_margin'            => (float) ($item['profit_margin'] ?? 0),
                    'selling_price'            => (float) ($item['selling_price'] ?? 0),
                ]);

                if ($request->purchase_status === 'received') {
                    Product::where('id', $item['product_id'])->increment('quantity', $qty);

                    if (!empty($item['selling_price'])) {
                        Product::where('id', $item['product_id'])
                            ->update(['selling_price' => (float) $item['selling_price']]);
                    }
                }
            }

            // ── 7. Save additional expenses ─────────────────────────

            foreach ($expenseNames as $i => $name) {
                if (!empty(trim((string) $name))) {
                    PurchaseAdditionalExpense::create([
                        'purchase_id' => $purchase->id,
                        'name'        => trim($name),
                        'amount'      => (float) ($expenseAmounts[$i] ?? 0),
                    ]);
                }
            }

            // ── 8. Save payment records ─────────────────────────────

            $paymentNote = $request->payment_note;

            foreach ($paymentAmounts as $i => $amt) {
                $amt       = (float) $amt;
                $methodId  = $paymentMethods[$i]  ?? null;
                $accountId = $paymentAccounts[$i] ?? null;
                $paidOn    = $paymentDates[$i]    ?? now();

                if ($amt > 0 && $methodId) {
                    Payment::create([
                        'purchase_id'        => $purchase->id,
                        'payment_method_id'  => $methodId,
                        'payment_account_id' => $accountId ?: null,
                        'amount'             => round($amt, 2),
                        'paid_on'            => Carbon::parse($paidOn),
                        'type'               => 'purchase_payment',
                        'note'               => $paymentNote,
                    ]);
                }
            }

            // ── 9. Update supplier's purchase_due ──────────────────
            //
            //  If any amount is still due, add it to the supplier's
            //  running purchase_due balance.
            //

            if ($amountDue > 0) {
                Supplier::where('id', $request->supplier_id)
                    ->increment('purchase_due', round($amountDue, 2));
            }

            DB::commit();

            return redirect()->route('all.purchase')
                ->with('success', "Purchase #{$referenceNo} saved successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to save: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ──────────────────────────────────────────────────────────
    // View Single Purchase
    // ──────────────────────────────────────────────────────────
    public function viewPurchase($id)
    {
        $purchase = Purchase::with([
            'supplier',
            'location',
            'createdBy',
            'items.product',
            'additionalExpenses',
            'payments.method',
            'payments.account',
        ])->findOrFail($id);

        return view('admin.purchase.view_purchase', compact('purchase'));
    }

    // ──────────────────────────────────────────────────────────
    // Delete Purchase (reverses stock AND supplier due if received/due)
    // ──────────────────────────────────────────────────────────
    public function deletePurchase($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Reverse stock if purchase was received
            if ($purchase->purchase_status === 'received') {
                foreach ($purchase->items as $item) {
                    Product::where('id', $item->product_id)->decrement('quantity', $item->qty);
                }
            }

            // Reverse supplier due balance for whatever is still unpaid
            $remainingDue = $purchase->amount_due ?? max(0, $purchase->purchase_total - $purchase->amount_paid);
            if ($remainingDue > 0) {
                Supplier::where('id', $purchase->supplier_id)
                    ->decrement('purchase_due', round($remainingDue, 2));
            }

            $purchase->delete();

            DB::commit();
            return redirect()->route('all.purchase')
                ->with('success', 'Purchase deleted and stock/supplier balance reversed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Show purchase as JSON
    // ──────────────────────────────────────────────────────────
    public function showPurchase($id)
    {
        $purchase = Purchase::with([
            'supplier', 'location', 'addedBy',
            'items.product', 'additionalExpenses',
            'payments.method', 'payments.account',
        ])->findOrFail($id);

        $purchase->append(['total_paid', 'payment_due']);

        return response()->json($purchase);
    }

    // ──────────────────────────────────────────────────────────
    // Print
    // ──────────────────────────────────────────────────────────
    public function printPurchase($id)
    {
        $purchase = Purchase::with([
            'supplier', 'location', 'items.product', 'payments'
        ])->findOrFail($id);

        return view('admin.backend.purchase.print_purchase', compact('purchase'));
    }

    // ──────────────────────────────────────────────────────────
    // Download document
    // ──────────────────────────────────────────────────────────
    public function downloadDocument($id)
    {
        $purchase = Purchase::findOrFail($id);

        if (!$purchase->document || !file_exists(public_path($purchase->document))) {
            return back()->withErrors(['error' => 'Document not found.']);
        }

        return response()->download(public_path($purchase->document));
    }

    // ──────────────────────────────────────────────────────────
    // View document in browser
    // ──────────────────────────────────────────────────────────
    public function viewDocument($id)
    {
        $purchase = Purchase::findOrFail($id);

        if (!$purchase->document || !file_exists(public_path($purchase->document))) {
            abort(404, 'Document not found.');
        }

        return response()->file(public_path($purchase->document));
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Add a single payment to an existing purchase
    // Also updates supplier purchase_due accordingly
    // ──────────────────────────────────────────────────────────
    public function addPayment(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'payment_method'     => 'required|string',
            'paid_on'            => 'required|date',
            'amount'             => 'required|numeric|min:0.01',
            'payment_account_id' => 'nullable|exists:payment_accounts,id',
            'document'           => 'nullable|file|max:5120|mimes:pdf,csv,zip,doc,docx,jpeg,jpg,png',
        ]);

        DB::beginTransaction();
        try {
            $documentPath = null;
            if ($request->hasFile('document')) {
                $file         = $request->file('document');
                $filename     = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload/purchase_documents'), $filename);
                $documentPath = 'upload/purchase_documents/' . $filename;
            }

            $paymentAmount = round((float) $request->amount, 2);

            Payment::create([
                'purchase_id'        => $purchase->id,
                'payment_method_id'  => $request->payment_method,
                'payment_account_id' => $request->payment_account_id ?: null,
                'amount'             => $paymentAmount,
                'paid_on'            => Carbon::parse($request->paid_on),
                'type'               => 'purchase_payment',
                'note'               => $request->payment_note,
                'document'           => $documentPath,
            ]);

            // Recalculate totals
            $totalPaid    = $purchase->payments()->sum('amount');
            $newAmountDue = max(0, $purchase->purchase_total - $totalPaid);

            if ($totalPaid <= 0) {
                $status = 'due';
            } elseif ($totalPaid < $purchase->purchase_total) {
                $status = 'partial';
            } else {
                $status = 'paid';
            }

            $oldAmountDue = $purchase->amount_due ?? 0;

            $purchase->update([
                'amount_paid'    => round($totalPaid, 2),
                'amount_due'     => round($newAmountDue, 2),
                'payment_status' => $status,
            ]);

            // Reduce supplier due by how much the due balance decreased
            $dueReduction = $oldAmountDue - $newAmountDue;
            if ($dueReduction > 0) {
                Supplier::where('id', $purchase->supplier_id)
                    ->decrement('purchase_due', round($dueReduction, 2));
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: View all payments for a purchase
    // ──────────────────────────────────────────────────────────
    public function viewPayments($id)
    {
        $purchase = Purchase::with([
            'supplier', 'location',
            'payments.method', 'payments.account',
        ])->findOrFail($id);

        return response()->json($purchase);
    }

    // ──────────────────────────────────────────────────────────
    // Edit Purchase form
    // ──────────────────────────────────────────────────────────
    public function editPurchase($id)
    {
        $purchase = Purchase::with(['items', 'additionalExpenses', 'payments'])->findOrFail($id);
        $suppliers      = Supplier::orderBy('name')->get();
        $paymentMethods = AccountType::orderBy('name')->get();
        $locations      = WareHouse::orderBy('name')->get();
        $taxes          = Tax::orderBy('name')->get();

        return view('admin.backend.purchase.edit_purchase', compact(
            'purchase', 'suppliers', 'paymentMethods', 'locations', 'taxes'
        ));
    }
}