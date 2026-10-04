<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsReminder extends Model {
    protected $primaryKey = 'sms_id';
    protected $fillable = ['owner_id', 'phone_number', 'message', 'sent_date', 'status'];

    public function owner() {
        return $this->belongsTo(Owner::class, 'owner_id', 'owner_id');
    }
    
}
