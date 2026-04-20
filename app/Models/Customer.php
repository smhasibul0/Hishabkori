<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    // Helper to get full individual name
    public function getFullNameAttribute()
    {
        return trim($this->prefix . ' ' . $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    // Helper to get display name (business or individual)
    public function getDisplayNameAttribute()
    {
        return $this->business_name
            ? $this->business_name
            : $this->full_name;
    }
}