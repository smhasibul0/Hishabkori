<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function subcategory(){
        return $this->belongsTo(ProductCategory::class, 'subcategory_id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function isLowStock(){
        return $this->manage_stock && $this->alert_quantity > 0 && $this->quantity <= $this->alert_quantity;
    }
}