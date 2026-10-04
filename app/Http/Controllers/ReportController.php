<?php
namespace App\Http\Controllers;

use App\Models\TaxRecord;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller {
    public function index(Request $request) {
        $year = (int) $request->get('year', date('Y'));

        $totalDue = TaxRecord::where('tax_year', $year)->sum('amount_due');
        $totalPaid = TaxRecord::where('tax_year', $year)->sum('amount_paid');
        $totalBalance = $totalDue - $totalPaid;
        $collectionRate = $totalDue > 0 ? round(($totalPaid / $totalDue) * 100, 1) : 0;

        $byClass = TaxRecord::where('tax_year', $year)
            ->join('properties', 'tax_records.property_id', '=', 'properties.property_id')
            ->join('property_classes', 'properties.property_class_id', '=', 'property_classes.id')
            ->selectRaw('property_classes.name as class_name, COUNT(*) cnt, SUM(amount_due) due, SUM(amount_paid) paid')
            ->groupBy('property_classes.name')->get();

        return view('reports.index', compact('year', 'totalDue', 'totalPaid', 'totalBalance', 'collectionRate', 'byClass'));
    }

    /**
     * Export the full tax records report for the given year as a CSV file
     * (opens directly in Excel). "Export" button on the Reports page.
     */
    public function export(Request $request): StreamedResponse {
        $year = (int) $request->get('year', date('Y'));

        $records = TaxRecord::with(['property.owner', 'property.propertyType', 'property.propertyClass'])
            ->where('tax_year', $year)
            ->orderBy('tax_id')
            ->get();

        $filename = "GHTCS-Tax-Report-{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['House ID', 'Owner', 'Phone', 'Type', 'Class', 'Amount Due', 'Amount Paid', 'Balance', 'Due Date', 'Status']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->property->house_id,
                    $r->property->owner->full_name,
                    $r->property->owner->phone_number,
                    $r->property->propertyType->name,
                    $r->property->propertyClass->name,
                    number_format($r->amount_due, 2),
                    number_format($r->amount_paid, 2),
                    number_format($r->amount_due - $r->amount_paid, 2),
                    $r->due_date,
                    $r->status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
