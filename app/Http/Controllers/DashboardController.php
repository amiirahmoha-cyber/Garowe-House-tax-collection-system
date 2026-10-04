<?php
namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\TaxRecord;
use App\Models\SmsReminder;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function index() {
        $year = date('Y');

        $totalProperties = Property::count();
        $totalOwners = Owner::count();
        $totalCollected = Payment::whereYear('payment_date', $year)->sum('amount');
        $pendingTaxes = TaxRecord::where('tax_year', $year)
            ->whereIn('status', ['Unpaid', 'Partial'])
            ->selectRaw('SUM(amount_due - amount_paid) as s')->value('s') ?? 0;
        $overdueTaxes = TaxRecord::where('tax_year', $year)->where('status', 'Overdue')
            ->selectRaw('SUM(amount_due - amount_paid) as s')->value('s') ?? 0;
        $smsToday = SmsReminder::whereDate('sent_date', now()->toDateString())->count();

        $monthly = array_fill(1, 12, 0);
        Payment::whereYear('payment_date', $year)
            ->selectRaw('MONTH(payment_date) m, SUM(amount) s')
            ->groupBy('m')->get()
            ->each(function ($row) use (&$monthly) { $monthly[(int)$row->m] = (float) $row->s; });

        $paidSum = TaxRecord::where('tax_year', $year)->where('status', 'Paid')->sum('amount_paid');
        $partialSum = TaxRecord::where('tax_year', $year)->where('status', 'Partial')->sum('amount_paid');
        $unpaidSum = TaxRecord::where('tax_year', $year)->where('status', 'Unpaid')->sum('amount_due');

        $classData = \App\Models\PropertyClass::orderBy('name')->get()->map(function ($class) use ($year) {
    $sum = \App\Models\Payment::whereYear('payment_date', $year)
        ->whereHas('property', fn($q) => $q->where('property_class_id', $class->id))
        ->sum('amount');
    return ['label' => $class->name, 'sum' => (float) $sum];
})->filter(fn($c) => $c['sum'] > 0)->values();

$classLabels = $classData->pluck('label');
$classSums = $classData->pluck('sum');

        $recentPayments = Payment::with(['property', 'owner'])->latest('payment_id')->limit(5)->get();

        $topUnpaid = TaxRecord::with('property.owner')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->get()
            ->sortByDesc(fn($t) => $t->amount_due - $t->amount_paid)
            ->take(5);

        $activities = ActivityLog::latest('log_id')->limit(5)->get();

       return view('dashboard', compact(
    'totalProperties', 'totalOwners', 'totalCollected', 'pendingTaxes', 'overdueTaxes',
    'smsToday', 'monthly', 'paidSum', 'partialSum', 'unpaidSum', 'classLabels', 'classSums',
    'recentPayments', 'topUnpaid', 'activities', 'year'
));
    }
}
