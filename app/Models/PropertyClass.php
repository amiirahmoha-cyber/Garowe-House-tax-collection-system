<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyClass extends Model {
    protected $fillable = ['name', 'rate_per_sqm', 'description'];

    public function properties() {
        return $this->hasMany(Property::class);
    }
}
