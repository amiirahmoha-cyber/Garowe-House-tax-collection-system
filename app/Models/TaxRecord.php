<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRecord extends Model {
    protected $primaryKey = 'tax_id';
    protected $fillable = [
        'property_id', 'tax_year', 'amount_due', 'amount_paid',
        'due_date', 'status', 'last_reminder_date'
    ];

    public function property() {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'tax_id', 'tax_id');
    }

    public function getBalanceAttribute() {
        return $this->amount_due - $this->amount_paid;
    }
}
