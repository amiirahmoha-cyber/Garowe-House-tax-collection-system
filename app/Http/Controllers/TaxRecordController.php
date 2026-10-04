<?php
namespace App\Http\Controllers;

use App\Models\TaxRecord;
use App\Models\Property;
use App\Models\ActivityLog;
use App\Services\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxRecordController extends Controller {
    public function index(Request $request) {
        $year = (int) $request->get('year', date('Y'));

        // Mark overdue
        TaxRecord::whereIn('status', ['Unpaid', 'Partial'])
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'Overdue']);

        $records = TaxRecord::with(['property.owner', 'property.propertyType'])
            ->where('tax_year', $year)
            ->orderByDesc('tax_id')->get();

        // Distinct years already generated (for the dropdown), always include current year
        $existingYears = TaxRecord::select('tax_year')->distinct()->pluck('tax_year')
            ->push(date('Y'))->unique()->sortDesc()->values();

        return view('tax_records.index', compact('records', 'year', 'existingYears'));
    }

    /**
     * "Meesha sanadka laga abuuri karo" — generate tax records for ALL active
     * properties for a YEAR TYPED IN BY THE USER (not limited to a fixed dropdown range).
     * Tax amount is recalculated live from each property's CURRENT square_meters x class rate.
     * As soon as a new tax record is created, an SMS notice is sent to the owner automatically.
     */
    public function generate(Request $request, SmsGateway $sms) {
        $request->validate(['year' => 'required|integer|min:2020|max:2100']);
        $year = (int) $request->year;
        $dueDate = "$year-12-31";
        $count = 0;

        Property::where('status', 'Active')->with(['propertyClass', 'owner'])->chunk(100, function ($properties) use ($year, $dueDate, &$count, $sms) {
            foreach ($properties as $property) {
                $exists = TaxRecord::where('property_id', $property->property_id)->where('tax_year', $year)->exists();
                if ($exists) continue;

                $amount = round($property->square_meters * ($property->propertyClass->rate_per_sqm ?? 0), 2);

                TaxRecord::create([
                    'property_id' => $property->property_id,
                    'tax_year' => $year,
                    'amount_due' => $amount,
                    'due_date' => $dueDate,
                    'status' => 'Unpaid',
                ]);
                $count++;

                // Automatic SMS notice to the owner as soon as their tax is generated
                if ($property->owner) {
                    $message = "Salaan {$property->owner->full_name}, canshuurta guriga {$property->house_id} ee sannadka {$year} waa la xisaabiyay: $"
                        . number_format($amount, 2) . ". Waxaad bixin kartaa kahor {$dueDate}. - Degmada Garowe";
                    $sms->send($property->owner, $property->owner->phone_number, $message);
                }
            }
        });

        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Generated tax records', 'details' => "$count records for $year (SMS sent automatically)"]);

        return redirect()->route('tax-records.index', ['year' => $year])->with('success', "$count tax record(s) la abuuray sannadka $year, oo SMS otomaatig ah loo diray milkiilayaasha.");
    }
}
