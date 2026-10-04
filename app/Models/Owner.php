<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model {
    protected $primaryKey = 'owner_id';
    protected $fillable = ['full_name', 'phone_number', 'national_id', 'email', 'address', 'district', 'status'];

    public function properties() {
        return $this->hasMany(Property::class, 'owner_id', 'owner_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'owner_id', 'owner_id');
    }

    public function smsReminders() {
        return $this->hasMany(SmsReminder::class, 'owner_id', 'owner_id');
    }
    protected static function booted(): void {
        static::creating(function ($owner) {
            if (empty($owner->registered_date)) {
                $owner->registered_date = now()->toDateString();
            }
        });
    }
}
