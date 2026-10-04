<!DOCTYPE html>
<html lang="so">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $payment->receipt_no }} - GHTCS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<style>
  body{ background:#eef1f6; }
  .receipt-page{ max-width:720px; margin:30px auto; }
  .receipt-box{ background:#fff; border-radius:14px; padding:36px 40px; box-shadow:0 1px 6px rgba(0,0,0,0.08); border-top:6px solid #2563eb; }
  .receipt-head{ display:flex; align-items:center; gap:16px; border-bottom:2px dashed #dfe4ee; padding-bottom:18px; margin-bottom:20px;}
  .receipt-head img{ width:70px; height:70px; }
  .receipt-title{ text-align:center; margin-bottom:20px; }
  .receipt-title h3{ margin:0; letter-spacing:1px; color:#2563eb; }
  table.receipt-table{ width:100%; font-size:14px; margin-bottom:18px; }
  table.receipt-table td{ padding:6px 4px; }
  table.receipt-table td.label{ color:#6b7688; width:45%; }
  table.receipt-table td.value{ font-weight:600; text-align:right; }
  .amount-box{ background:#eaf1ff; border-radius:10px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
  .amount-box .amt{ font-size:26px; font-weight:800; color:#2563eb; }
  .receipt-footer{ text-align:center; font-size:12px; color:#9ca3af; border-top:1px dashed #dfe4ee; padding-top:14px; }
  .no-print{ text-align:center; margin:20px 0; }
  @media print { body{ background:#fff; } .no-print{ display:none; } .receipt-page{ margin:0; } .receipt-box{ box-shadow:none; border-radius:0; } }
</style>
</head>
<body>
<div class="receipt-page">
    <div class="receipt-box">
        <div class="receipt-head">
            <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'">
            <div>
                <h2 style="margin:0; font-size:19px;">Dowladda Hoose ee Degmada Garowe</h2>
                <p style="margin:0; font-size:12.5px; color:#6b7688;">Garowe Local Government — House Tax Collection System</p>
                <p style="margin:0; font-size:12.5px; color:#6b7688;">District: Garowe, Puntland, Somalia</p>
            </div>
        </div>

        <div class="receipt-title">
            <h3>OFFICIAL PAYMENT RECEIPT</h3>
            <span style="font-size:12.5px; color:#6b7688;">Rasiidka Rasmiga ah ee Lacag-bixinta Canshuurta Guriga</span>
        </div>

        <table class="receipt-table">
            <tr><td class="label">Receipt No</td><td class="value">{{ $payment->receipt_no }}</td></tr>
            <tr><td class="label">Payment Date</td><td class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y') }}</td></tr>
            <tr><td class="label">House ID</td><td class="value">{{ $payment->property->house_id }}</td></tr>
            <tr><td class="label">Owner Name</td><td class="value">{{ $payment->owner->full_name }}</td></tr>
            <tr><td class="label">Phone Number</td><td class="value">{{ $payment->owner->phone_number }}</td></tr>
            <tr><td class="label">Property Type / Class</td><td class="value">{{ $payment->property->propertyType->name }} — {{ $payment->property->propertyClass->name }}</td></tr>
            <tr><td class="label">District</td><td class="value">{{ $payment->property->district }}</td></tr>
            <tr><td class="label">Tax Year</td><td class="value">{{ $payment->taxRecord->tax_year }}</td></tr>
            <tr><td class="label">Payment Method</td><td class="value">{{ $payment->payment_method }}</td></tr>
        </table>

        <div class="amount-box">
            <div>Amount Paid (this receipt)</div>
            <div class="amt">${{ number_format($payment->amount, 2) }}</div>
        </div>

        <table class="receipt-table">
            <tr><td class="label">Total Tax Due ({{ $payment->taxRecord->tax_year }})</td><td class="value">${{ number_format($payment->taxRecord->amount_due, 2) }}</td></tr>
            <tr><td class="label">Total Paid to Date</td><td class="value">${{ number_format($payment->taxRecord->amount_paid, 2) }}</td></tr>
            <tr><td class="label">Remaining Balance</td><td class="value" style="color:{{ $payment->taxRecord->balance > 0 ? '#dc2626' : '#16a34a' }};">${{ number_format($payment->taxRecord->balance, 2) }}</td></tr>
            <tr><td class="label">Status</td><td class="value"><span class="badge badge-{{ strtolower($payment->taxRecord->status) }}">{{ $payment->taxRecord->status }}</span></td></tr>
            <tr><td class="label">Received By</td><td class="value">{{ $payment->collector->full_name ?? 'System' }}</td></tr>
        </table>

        <div class="receipt-footer">
            Rasiidkan waa caddayn rasmi ah oo ka socota Degmada Garowe.<br>
            This is a computer-generated receipt from the Garowe House Tax Collection System.
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print Receipt</button>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Back to Payments</a>
    </div>
</div>
</body>
</html>
