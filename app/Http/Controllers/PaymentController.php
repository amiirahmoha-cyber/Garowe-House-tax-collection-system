<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\TaxRecord;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function index(Request $request) {
        $search = trim($request->get('q', ''));

        $payments = Payment::with(['property', 'owner', 'taxRecord'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('receipt_no', 'like', "%$search%")
                    ->orWhereHas('property', fn($p) => $p->where('house_id', 'like', "%$search%"))
                    ->orWhereHas('owner', fn($o) => $o->where('full_name', 'like', "%$search%"));
            })
            ->orderByDesc('payment_id')->get();

        return view('payments.index', compact('payments', 'search'));
    }

    public function create(Request $request) {
        $unpaid = TaxRecord::with('property.owner')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->orderByDesc('tax_id')->get();
        $preselectTaxId = (int) $request->get('tax_id', 0);
        return view('payments.create', compact('unpaid', 'preselectTaxId'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'tax_id' => 'required|exists:tax_records,tax_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,EVC Plus,Golis Pesa,Bank Transfer',
        ]);

        $tax = TaxRecord::findOrFail($data['tax_id']);
        $property = $tax->property;

        $receiptNo = Payment::generateReceiptNo();
        $newPaid = $tax->amount_paid + $data['amount'];
        $balance = $tax->amount_due - $newPaid;
        $status = $balance <= 0 ? 'Paid' : 'Partial';

        $payment = Payment::create([
            'receipt_no' => $receiptNo,
            'tax_id' => $tax->tax_id,
            'property_id' => $property->property_id,
            'owner_id' => $property->owner_id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'received_by' => auth()->id(),
            'status' => $status,
        ]);

        $tax->update(['amount_paid' => $newPaid, 'status' => $status]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'New payment recorded',
            'details' => "$receiptNo for $" . number_format($data['amount'], 2) . " ({$property->house_id})",
        ]);

        return redirect()->route('receipts.show', $payment->payment_id);
    }
}