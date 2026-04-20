<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'purchase_date'    => 'datetime',   // actual column name in migration
        'purchase_total'   => 'decimal:2',  // actual column name in migration
        'subtotal'         => 'decimal:2',
        'net_total'        => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'amount_due'       => 'decimal:2',
    ];

    /* ─── Relationships ─── */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location()
    {
        return $this->belongsTo(WareHouse::class, 'business_location_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function additionalExpenses()
    {
        return $this->hasMany(PurchaseAdditionalExpense::class);
    }

    /* ─── Accessors ─── */

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    // Uses 'purchase_total' — the actual DB column
    public function getPaymentDueAttribute(): float
    {
        return max(0, (float) $this->purchase_total - $this->total_paid);
    }

    // Alias so views/JSON can use 'grand_total' if needed
    public function getGrandTotalAttribute(): float
    {
        return (float) $this->purchase_total;
    }

    /* ─── Scopes ─── */

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('purchase_status', $status);
    }

    public function scopeByPaymentStatus($query, string $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('purchase_date', [$from, $to]);
    }

    /* ─── Helpers ─── */

    public static function generateReferenceNo(): string
    {
        $year = now()->format('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'PO' . $year . '/' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    // Append these so they always appear in JSON responses
    protected $appends = ['total_paid', 'payment_due', 'grand_total'];
}