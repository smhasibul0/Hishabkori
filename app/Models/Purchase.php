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
        'date'             => 'datetime',
        'grand_total'      => 'decimal:2',
        'total_before_tax' => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'purchase_total'   => 'decimal:2',
    ];

    /* ─── Relationships ─── */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location()
    {
        return $this->belongsTo(Warehouse::class);
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
        return $this->belongsTo(User::class, 'added_by');
    }

    /* ─── Accessors ─── */

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    public function getPaymentDueAttribute(): float
    {
        return max(0, $this->grand_total - $this->total_paid);
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
        return $query->whereBetween('date', [$from, $to]);
    }

    /* ─── Helpers ─── */

    public static function generateReferenceNo(): string
    {
        $year  = now()->format('Y');
        $last  = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'PO' . $year . '/' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
    public function additionalExpenses()
    {
        return $this->hasMany(PurchaseAdditionalExpense::class);
    }

    // Add this so total_paid and payment_due always appear in JSON responses
    protected $appends = ['total_paid', 'payment_due'];
}