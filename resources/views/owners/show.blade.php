@extends('layouts.app')
@section('title', $owner->full_name . ' - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>👤 {{ $owner->full_name }}</h1><p>Owner Profile & Property Overview</p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('owners.edit', $owner) }}" class="btn btn-primary">✏️ Edit</a>
        <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary">← Back</a>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>Owner Details</h3></div>
        <table class="data-table">
            <tr><td class="text-muted" style="width:40%;">Full Name</td><td><strong>{{ $owner->full_name }}</strong></td></tr>
            <tr><td class="text-muted">Phone Number</td><td>{{ $owner->phone_number }}</td></tr>
            <tr><td class="text-muted">National ID</td><td>{{ $owner->national_id ?: '—' }}</td></tr>
            <tr><td class="text-muted">Email</td><td>{{ $owner->email ?: '—' }}</td></tr>
            <tr><td class="text-muted">Address</td><td>{{ $owner->address ?: '—' }}</td></tr>
            <tr><td class="text-muted">District</td><td>{{ $owner->district }}</td></tr>
            <tr><td class="text-muted">Status</td><td><span class="badge {{ $owner->status=='Active'?'badge-paid':'badge-unpaid' }}">{{ $owner->status }}</span></td></tr>
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Tax Summary</h3></div>
        <table class="data-table">
            <tr><td class="text-muted" style="width:50%;">Total Properties</td><td><strong>{{ $owner->properties->count() }}</strong></td></tr>
            <tr><td class="text-muted">Total Paid (all time)</td><td style="color:#16a34a; font-weight:700;">${{ number_format($totalPaid, 2) }}</td></tr>
            <tr><td class="text-muted">Total Outstanding Balance</td><td style="color:#dc2626; font-weight:700;">${{ number_format($totalDue, 2) }}</td></tr>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h3>Properties Owned</h3><a href="{{ route('properties.create') }}">➕ Add Property</a></div>
    <table class="data-table">
        <tr><th>House ID</th><th>Type</th><th>Class</th><th>Rooms</th><th>Size (m²)</th><th>Annual Tax</th><th></th></tr>
        @foreach($owner->properties as $p)
        <tr>
            <td style="font-weight:700; color:var(--navy);">{{ $p->house_id }}</td>
            <td>{{ $p->propertyType->name }}</td>
            <td>{{ $p->propertyClass->name }}</td>
            <td>{{ $p->rooms }}</td>
            <td>{{ number_format($p->square_meters, 1) }}</td>
            <td>${{ number_format($p->annual_tax, 2) }}</td>
            <td><a href="{{ route('properties.show', $p) }}" class="action-btn btn-view">View</a></td>
        </tr>
        @endforeach
    </table>
</div>

<div class="panel">
    <div class="panel-header"><h3>Recent Payments</h3></div>
    <table class="data-table">
        <tr><th>Receipt No</th><th>House ID</th><th>Amount</th><th>Date</th><th>Status</th><th></th></tr>
        @foreach($payments as $pay)
        <tr>
            <td>{{ $pay->receipt_no }}</td>
            <td>{{ $pay->property->house_id }}</td>
            <td>${{ number_format($pay->amount, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($pay->payment_date)->format('M j, Y') }}</td>
            <td><span class="badge badge-{{ strtolower($pay->status) }}">{{ $pay->status }}</span></td>
            <td><a href="{{ route('receipts.show', $pay) }}">🖨️</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
