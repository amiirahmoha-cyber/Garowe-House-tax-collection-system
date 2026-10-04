<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyClass;
use App\Models\Village;
use App\Models\TaxRecord;
use App\Models\Payment;
use Illuminate\Database\Seeder;

/**
 * Optional: run with `php artisan db:seed --class=SampleDataSeeder`
 * AFTER the main DatabaseSeeder, to populate sample owners/properties/
 * payments so the dashboard has data to display for testing.
 */
class SampleDataSeeder extends Seeder {
    public function run(): void {
        $residential = PropertyType::where('name', 'Residential')->first();
        $commercial = PropertyType::where('name', 'Commercial')->first();
        $classA = PropertyClass::where('name', 'Class A')->first();
        $classB = PropertyClass::where('name', 'Class B')->first();
        $classC = PropertyClass::where('name', 'Class C')->first();
        $wadajir = Village::where('name', 'Wadajir')->first();

        $names = ['Ahmed Mahamud Ali', 'Hassan Abdi Yusuf', 'Abdullahi Mohamed', 'Maryan Said Omar', 'Ali Osman Farah'];
        $owners = [];
        foreach ($names as $i => $name) {
            $owners[] = Owner::create([
                'full_name' => $name,
                'phone_number' => '09071234' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'district' => 'Garowe',
            ]);
        }

        $configs = [
            ['owner' => 0, 'type' => $residential, 'class' => $classB, 'rooms' => 4, 'sqm' => 120],
            ['owner' => 1, 'type' => $residential, 'class' => $classC, 'rooms' => 3, 'sqm' => 90],
            ['owner' => 2, 'type' => $commercial,  'class' => $classA, 'rooms' => 6, 'sqm' => 200],
            ['owner' => 3, 'type' => $residential, 'class' => $classB, 'rooms' => 5, 'sqm' => 150],
            ['owner' => 4, 'type' => $residential, 'class' => $classC, 'rooms' => 3, 'sqm' => 95],
        ];

        foreach ($configs as $cfg) {
            $tax = round($cfg['sqm'] * $cfg['class']->rate_per_sqm, 2);

            $property = Property::create([
                'house_id' => Property::generateHouseId($cfg['type']),
                'owner_id' => $owners[$cfg['owner']]->owner_id,
                'property_type_id' => $cfg['type']->id,
                'property_class_id' => $cfg['class']->id,
                'village_id' => $wadajir->id ?? null,
                'rooms' => $cfg['rooms'],
                'square_meters' => $cfg['sqm'],
                'district' => 'Garowe',
                'annual_tax' => $tax,
            ]);

            $taxRecord = TaxRecord::create([
                'property_id' => $property->property_id,
                'tax_year' => date('Y'),
                'amount_due' => $tax,
                'due_date' => date('Y') . '-12-31',
                'status' => 'Unpaid',
            ]);

            // Make a couple of them fully/partially paid for realistic demo data
            if (in_array($cfg['owner'], [0, 2])) {
                $paid = $tax; // fully paid
                Payment::create([
                    'receipt_no' => Payment::generateReceiptNo(),
                    'tax_id' => $taxRecord->tax_id,
                    'property_id' => $property->property_id,
                    'owner_id' => $owners[$cfg['owner']]->owner_id,
                    'amount' => $paid,
                    'payment_method' => 'Cash',
                    'status' => 'Paid',
                ]);
                $taxRecord->update(['amount_paid' => $paid, 'status' => 'Paid']);
            } elseif ($cfg['owner'] === 1) {
                $paid = round($tax / 2, 2);
                Payment::create([
                    'receipt_no' => Payment::generateReceiptNo(),
                    'tax_id' => $taxRecord->tax_id,
                    'property_id' => $property->property_id,
                    'owner_id' => $owners[$cfg['owner']]->owner_id,
                    'amount' => $paid,
                    'payment_method' => 'EVC Plus',
                    'status' => 'Partial',
                ]);
                $taxRecord->update(['amount_paid' => $paid, 'status' => 'Partial']);
            }
        }
    }
}
