<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model {
    protected $fillable = ['name', 'district'];

    public function properties() {
        return $this->hasMany(Property::class);
    }
}
