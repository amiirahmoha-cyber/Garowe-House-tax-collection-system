<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model {
    protected $primaryKey = 'property_id';
    protected $fillable = [
        'house_id', 'owner_id', 'property_type_id', 'property_class_id', 'village_id',
        'rooms', 'square_meters', 'district', 'address', 'annual_tax', 'status'
    ];

    public function owner() {
        return $this->belongsTo(Owner::class, 'owner_id', 'owner_id');
    }

    public function propertyType() {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function propertyClass() {
        return $this->belongsTo(PropertyClass::class, 'property_class_id');
    }

    public function village() {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function taxRecords() {
        return $this->hasMany(TaxRecord::class, 'property_id', 'property_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'property_id', 'property_id');
    }

    // Tax is always computed live from the CURRENT square meters * class rate
    // ("cabirka guriga" drives the tax amount - never a stale stored value)
    public function calculateAnnualTax(): float {
        $rate = $this->propertyClass->rate_per_sqm ?? 0;
        return round($this->square_meters * $rate, 2);
    }

    // Auto-generate House ID like GR-RES-0001 / GR-COM-0001 based on type name
    public static function generateHouseId(PropertyType $type): string {
        $short = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $type->name), 0, 3));
        $prefix = "GR-{$short}-";
        $last = static::where('house_id', 'like', $prefix . '%')
            ->orderByDesc('property_id')->first();
        $next = $last ? ((int) substr($last->house_id, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
    protected static function booted(): void {
        static::creating(function ($property) {
            if (empty($property->registered_date)) {
                $property->registered_date = now()->toDateString();
            }
        });
    }
}
