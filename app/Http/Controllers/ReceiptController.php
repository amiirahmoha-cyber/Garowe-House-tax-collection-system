<?php
namespace App\Http\Controllers;

use App\Models\Payment;

class ReceiptController extends Controller {
    public function show(Payment $payment) {
        $payment->load(['property.propertyType', 'property.propertyClass', 'owner', 'taxRecord', 'collector']);
        return view('receipts.show', compact('payment'));
    }
}
