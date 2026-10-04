@extends('layouts.app')
@section('title', 'Payments - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Payments</h1><p>All tax payments recorded in the system.</p></div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">➕ Record Payment</a>
</div>

<div class="panel">
    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width:320px; margin-bottom:0;"
               placeholder="Search by House ID or Owner name..." value="{{ $search }}">
        <button class="btn btn-outline-secondary">Search</button>
    </form>

    <table class="data-table">
        <tr><th>Receipt No</th><th>House ID</th><th>Owner</th><th>Amount Due</th><th>Amount Paid</th><th>Balance</th><th>Method</th><th>Date</th><th>Status</th><th></th></tr>
        @foreach($payments as $p)
        <tr>
            <td>{{ $p->receipt_no }}</td>
            <td style="font-weight:700; color:var(--navy);">{{ $p->property->house_id }}</td>
            <td>{{ $p->owner->full_name }}</td>
            <td>${{ number_format($p->taxRecord->amount_due ?? 0, 2) }}</td>
            <td>${{ number_format($p->taxRecord->amount_paid ?? 0, 2) }}</td>
            <td style="color:{{ ($p->taxRecord->balance ?? 0) > 0 ? '#dc2626' : '#16a34a' }}; font-weight:600;">
                ${{ number_format(max($p->taxRecord->balance ?? 0, 0), 2) }}
            </td>
            <td>{{ $p->payment_method }}</td>
            <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M j, Y') }}</td>
            <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
            <td>
                <a href="{{ route('receipts.show', $p) }}" title="Print Receipt">🖨️</a>
                @if(($p->taxRecord->balance ?? 0) > 0)
                    <a href="{{ route('payments.create') }}?tax_id={{ $p->tax_id }}" class="action-btn btn-view" style="margin-left:6px;">Record Payment</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection