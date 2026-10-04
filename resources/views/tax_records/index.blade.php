@extends('layouts.app')
@section('title', 'Tax Records - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Tax Records</h1><p>Yearly tax generated per registered property (canshuurta waxaa laga xisaabiyaa cabirka guriga - square meters).</p></div>
</div>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">View Year:</label>
            <select name="year" class="form-control" style="margin-bottom:0; width:120px;" onchange="this.form.submit()">
                @foreach($existingYears as $y)
                    <option value="{{ $y }}" {{ $y==$year?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        <!-- "Meesha sanadka laga abuuri karo" - type ANY year and generate tax for it -->
        <form method="POST" action="{{ route('tax-records.generate') }}" class="d-flex gap-2 align-items-center"
              onsubmit="return confirm('Generate tax records dhammaan guryaha sannadkan?')">
            @csrf
            <label class="form-label mb-0">Generate for Year:</label>
            <input type="number" name="year" class="form-control" style="width:120px; margin-bottom:0;" value="{{ date('Y') }}" min="2020" max="2100" required>
            <button type="submit" class="btn btn-primary">⚙️ Generate Tax</button>
        </form>
    </div>
</div>

<div class="panel">
    <table class="data-table">
        <tr><th>House ID</th><th>Owner</th><th>Type</th><th>Amount Due</th><th>Amount Paid</th><th>Balance</th><th>Due Date</th><th>Status</th><th></th></tr>
        @foreach($records as $r)
        <tr>
            <td style="font-weight:700; color:var(--navy);">{{ $r->property->house_id }}</td>
            <td>{{ $r->property->owner->full_name }}</td>
            <td>{{ $r->property->propertyType->name }}</td>
            <td>${{ number_format($r->amount_due, 2) }}</td>
            <td>${{ number_format($r->amount_paid, 2) }}</td>
            <td>${{ number_format($r->amount_due - $r->amount_paid, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($r->due_date)->format('M j, Y') }}</td>
            <td><span class="badge badge-{{ strtolower($r->status) }}">{{ $r->status }}</span></td>
            <td>
                @if($r->status != 'Paid')
                <a href="{{ route('payments.create') }}?tax_id={{ $r->tax_id }}" class="action-btn btn-view">Record Payment</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
