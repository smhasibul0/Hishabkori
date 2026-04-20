<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
 
    public function location()
    {
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }
 
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    /** All line items for this purchase */
    public function items()
    {
        return $this->hasMany(PurchaseProduct::class);
    }
 
    /** Alias — use whichever feels natural */
    public function products()
    {
        return $this->hasMany(PurchaseProduct::class);
    }
 
    /** All payments made against this purchase */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
 
    /** Additional expenses (port fees, customs, etc.) */
    public function additionalExpenses()
    {
        return $this->hasMany(PurchaseAdditionalExpense::class);
    }
 
    // ── Computed Helpers ───────────────────────────────────
 
    /** Total actually paid across all payment records */
    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }
 
    /** Remaining due amount */
    public function balanceDue(): float
    {
        return max(0, (float) $this->purchase_total - $this->totalPaid());
    }
 
    /** Recalculate and save payment_status, amount_paid, amount_due */
    public function refreshPaymentStatus(): void
    {
        $paid  = $this->totalPaid();
        $total = (float) $this->purchase_total;
 
        $status = 'due';
        if ($paid >= $total && $total > 0) {
            $status = 'paid';
        } elseif ($paid > 0) {
            $status = 'partial';
        }
 
        $this->update([
            'amount_paid'    => $paid,
            'amount_due'     => max(0, $total - $paid),
            'payment_status' => $status,
        ]);
    }
}
