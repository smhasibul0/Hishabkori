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
        $purchases = Purchase::with(['supplier', 'payments', 'items.product'])
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
    // GET /purchase/search-products?q=...
    // ──────────────────────────────────────────────────────────
    public function searchProducts(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::where('is_active', 1)
            ->where(function ($query) use ($q) {
                $query->where('product_name', 'LIKE', "%{$q}%")
                      ->orWhere('product_code', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'product_name', 'product_code', 'purchase_price', 'selling_price', 'quantity', 'unit']);

        return response()->json(['products' => $products]);
    }

    // ──────────────────────────────────────────────────────────
    // AJAX: Payment Accounts by Method
    // GET /purchase/payment-accounts?method_id=...
    // ──────────────────────────────────────────────────────────
    public function getPaymentAccounts(Request $request)
    {
        $accounts = PaymentAccount::where('payment_method_id', $request->method_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'account_number']);

        return response()->json(['accounts' => $accounts]);
    }

    // ──────────────────────────────────────────────────────────
    // Store Purchase — saves EVERYTHING including multiple payments
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

            // Subtotal from line items
            $subtotal = array_sum(array_column($products, 'line_total'));

            // Order-level discount
            $discountType   = $request->discount_type ?? 'none';
            $discountValue  = (float) ($request->discount_value ?? 0);
            $discountAmount = 0;
            if ($discountType === 'fixed')   $discountAmount = $discountValue;
            if ($discountType === 'percent') $discountAmount = $subtotal * ($discountValue / 100);

            $afterDiscount = $subtotal - $discountAmount;

            // Tax
            $taxRate   = (float) ($request->purchase_tax ?? 0);
            $taxAmount = $afterDiscount * ($taxRate / 100);
            $netTotal  = $afterDiscount + $taxAmount;

            // Shipping + extra expenses
            $shippingCharges = (float) ($request->shipping_charges ?? 0);
            $extraExpenses   = 0;
            $expenseNames    = $request->expense_name    ?? [];
            $expenseAmounts  = $request->expense_amount  ?? [];
            foreach ($expenseAmounts as $a) $extraExpenses += (float) $a;

            $purchaseTotal = $netTotal + $shippingCharges + $extraExpenses;

            // ── 2. Total paid across ALL payment rows ───────────────

            $paymentAmounts  = $request->payment_amount  ?? [];
            $paymentDates    = $request->paid_on          ?? [];
            $paymentMethods  = $request->payment_method   ?? [];
            $paymentAccounts = $request->payment_account  ?? [];

            // Sum only rows where amount > 0 AND method is selected
            $totalPaid = 0;
            foreach ($paymentAmounts as $i => $amt) {
                $amt = (float) $amt;
                if ($amt > 0 && !empty($paymentMethods[$i])) {
                    $totalPaid += $amt;
                }
            }

            // Payment status
            $amountDue = max(0, $purchaseTotal - $totalPaid);
            if ($totalPaid <= 0) {
                $paymentStatus = 'due';
            } elseif ($totalPaid < $purchaseTotal) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'paid';
            }

            // ── 3. Handle document upload ───────────────────────────

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
                'shipping_charges'     => $shippingCharges,
                'subtotal'             => round($subtotal, 2),
                'net_total'            => round($netTotal, 2),
                'purchase_total'       => round($purchaseTotal, 2),
                'payment_status'       => $paymentStatus,
                'amount_paid'          => round($totalPaid, 2),
                'amount_due'           => round($amountDue, 2),
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
                    'unit'                     => $item['unit']           ?? 'Pieces',
                    'unit_cost'                => $unitCost,
                    'discount_percent'         => $discPct,
                    'unit_cost_after_discount' => round($unitCostAftDisc, 2),
                    'line_total'               => $lineTotal,
                    'profit_margin'            => (float) ($item['profit_margin']  ?? 0),
                    'selling_price'            => (float) ($item['selling_price']  ?? 0),
                ]);

                // Increment stock only on 'received'
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

            // ── 8. Save MULTIPLE payment records ────────────────────
            //
            //  Each index i in the payment_amount[] array is one payment row.
            //  We save a Payment row only when:
            //    - amount > 0
            //    - a payment method is selected
            //
            $paymentNote = $request->payment_note;

            foreach ($paymentAmounts as $i => $amt) {
                $amt      = (float) $amt;
                $methodId = $paymentMethods[$i]  ?? null;
                $accountId= $paymentAccounts[$i] ?? null;
                $paidOn   = $paymentDates[$i]    ?? now();

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
    // View Single Purchase (full details)
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
    // Delete Purchase (reverses stock if received)
    // ──────────────────────────────────────────────────────────
    public function deletePurchase($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($purchase->purchase_status === 'received') {
                foreach ($purchase->items as $item) {
                    Product::where('id', $item->product_id)
                        ->decrement('quantity', $item->qty);
                }
            }

            $purchase->delete(); // cascades to items, payments, expenses

            DB::commit();
            return redirect()->route('all.purchase')
                ->with('success', 'Purchase deleted and stock reversed.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Delete failed: ' . $e->getMessage()]);
        }
    }
}