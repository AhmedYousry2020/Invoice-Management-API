<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id','tenant_id', 'amount', 'payment_method', 'reference_number', 'paid_at'
    ];
    
    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'paid_at' => 'datetime',
    ];
    //protected static function booted()
    // {
    //     static::addGlobalScope(new TenantScope);
    // }
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
