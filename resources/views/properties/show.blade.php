@extends('layouts.app')
@section('title', $property->house_id . ' - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>🏠 {{ $property->house_id }}</h1><p>Property Details & Tax History</p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('properties.edit', $property) }}" class="btn btn-primary">✏️ Edit</a>
        <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>Property Details</h3></div>
        <table class="data-table">
            <tr><td class="text-muted" style="width:40%;">House ID</td><td><strong>{{ $property->house_id }}</strong></td></tr>
            <tr><td class="text-muted">Owner</td><td><a href="{{ route('owners.show', $property->owner) }}">{{ $property->owner->full_name }}</a> ({{ $property->owner->phone_number }})</td></tr>
            <tr><td class="text-muted">Property Type</td><td>{{ $property->propertyType->name }}</td></tr>
            <tr><td class="text-muted">Property Class</td><td>{{ $property->propertyClass->name }} (${{ number_format($property->propertyClass->rate_per_sqm,2) }}/m²)</td></tr>
            <tr><td class="text-muted">Rooms</td><td>{{ $property->rooms }}</td></tr>
            <tr><td class="text-muted">Size</td><td>{{ number_format($property->square_meters, 2) }} m²</td></tr>
            <tr><td class="text-muted">District</td><td>{{ $property->district }}</td></tr>
            <tr><td class="text-muted">Neighborhood</td><td>{{ $property->village->name ?? '—' }}</td></tr>
            <tr><td class="text-muted">Annual Tax</td><td><strong>${{ number_format($property->annual_tax, 2) }}</strong></td></tr>
            <tr><td class="text-muted">Status</td><td><span class="badge {{ $property->status=='Active'?'badge-paid':'badge-unpaid' }}">{{ $property->status }}</span></td></tr>
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Tax Records</h3></div>
        <table class="data-table">
            <tr><th>Year</th><th>Due</th><th>Paid</th><th>Balance</th><th>Status</th></tr>
            @foreach($taxRecords as $t)
            <tr>
                <td>{{ $t->tax_year }}</td>
                <td>${{ number_format($t->amount_due,2) }}</td>
                <td>${{ number_format($t->amount_paid,2) }}</td>
                <td>${{ number_format($t->amount_due - $t->amount_paid,2) }}</td>
                <td><span class="badge badge-{{ strtolower($t->status) }}">{{ $t->status }}</span></td>
            </tr>
            @endforeach
        </table>
        <a href="{{ route('payments.create') }}?tax_id={{ $taxRecords->first()->tax_id ?? '' }}" class="btn btn-outline-primary btn-sm mt-2">💳 Record Payment</a>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h3>Payment History</h3></div>
    <table class="data-table">
        <tr><th>Receipt No</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th></th></tr>
        @foreach($payments as $p)
        <tr>
            <td>{{ $p->receipt_no }}</td>
            <td>${{ number_format($p->amount,2) }}</td>
            <td>{{ $p->payment_method }}</td>
            <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M j, Y') }}</td>
            <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
            <td><a href="{{ route('receipts.show', $p) }}">🖨️</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
