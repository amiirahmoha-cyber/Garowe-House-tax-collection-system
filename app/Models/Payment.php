<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'receipt_no', 'tax_id', 'property_id', 'owner_id', 'amount',
        'payment_method', 'payment_date', 'received_by', 'status'
    ];

    public function taxRecord() {
        return $this->belongsTo(TaxRecord::class, 'tax_id', 'tax_id');
    }

    public function property() {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function owner() {
        return $this->belongsTo(Owner::class, 'owner_id', 'owner_id');
    }

    public function collector() {
        return $this->belongsTo(User::class, 'received_by', 'user_id');
    }

    public static function generateReceiptNo(): string {
        $year = date('Y');
        $prefix = "RCT-$year-";
        $last = static::where('receipt_no', 'like', $prefix . '%')
            ->orderByDesc('payment_id')->first();
        $next = $last ? ((int) substr($last->receipt_no, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
    protected static function booted(): void {
        static::creating(function ($payment) {
            if (empty($payment->payment_date)) {
                $payment->payment_date = now()->toDateString();
            }
        });
    }
}
