<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['full_name', 'username', 'password', 'role', 'status'];
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool {
        return $this->role === 'Administrator';
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'received_by', 'user_id');
    }
}
