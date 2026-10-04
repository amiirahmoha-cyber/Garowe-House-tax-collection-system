<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PropertyType;
use App\Models\PropertyClass;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Default admin login: admin / admin123
        User::create([
            'full_name' => 'System Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'Administrator',
            'status' => 'Active',
        ]);

        // A regular Collector account, per the request that normal users
        // should only see Payments/Properties/Owners (not tax/reports/logs)
        User::create([
            'full_name' => 'Tax Collector',
            'username' => 'collector',
            'password' => Hash::make('collector123'),
            'role' => 'Collector',
            'status' => 'Active',
        ]);

        // Property Types (manageable — more can be added from the app)
        PropertyType::insert([
            ['name' => 'Residential', 'description' => 'Guryaha la deggan yahay', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Commercial', 'description' => 'Dukaamada iyo ganacsiga', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Property Classes with rate per square meter (manageable)
        PropertyClass::insert([
            ['name' => 'Class A', 'rate_per_sqm' => 5.00, 'description' => 'Guryaha qiimaha sare', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Class B', 'rate_per_sqm' => 3.00, 'description' => 'Guryaha heerka dhexe', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Class C', 'rate_per_sqm' => 2.00, 'description' => 'Guryaha caadiga ah', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Villages / Neighborhoods (Xaafadaha Garowe) — manageable
        $villages = ['Wadajir', 'Israac', 'Wisil', 'Buule', 'Roox-caleemo', 'Kalabaydh', 'Fidow', 'Horseed', 'Shabelle'];
        foreach ($villages as $v) {
            Village::create(['name' => $v, 'district' => 'Garowe']);
        }
    }
}
