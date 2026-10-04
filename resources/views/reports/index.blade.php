@extends('layouts.app')
@section('title', 'Reports - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Reports</h1><p>Tax collection summary for {{ $year }}.</p></div>
    <form method="GET">
        <select name="year" class="form-control" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y')-3; $y--)
                <option value="{{ $y }}" {{ $y==$year?'selected':'' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<div class="text-end mb-3">
    <a href="{{ route('reports.export', ['year' => $year]) }}" class="btn btn-outline-primary">⬇️ Export CSV (Excel)</a>
</div>

<div class="stats-row">
    <div class="stat-card"><div class="label">Total Tax Billed</div><div class="value">${{ number_format($totalDue, 2) }}</div></div>
    <div class="stat-card"><div class="label">Total Collected</div><div class="value" style="color:#16a34a;">${{ number_format($totalPaid, 2) }}</div></div>
    <div class="stat-card"><div class="label">Outstanding Balance</div><div class="value" style="color:#dc2626;">${{ number_format($totalBalance, 2) }}</div></div>
    <div class="stat-card"><div class="label">Collection Rate</div><div class="value">{{ $collectionRate }}%</div></div>
</div>

<div class="panel">
    <div class="panel-header"><h3>Breakdown by Property Class</h3></div>
    <table class="data-table">
        <tr><th>Class</th><th>Properties</th><th>Total Due</th><th>Total Paid</th><th>Balance</th></tr>
        @foreach($byClass as $r)
        <tr>
            <td>{{ $r->class_name }}</td>
            <td>{{ $r->cnt }}</td>
            <td>${{ number_format($r->due, 2) }}</td>
            <td>${{ number_format($r->paid, 2) }}</td>
            <td>${{ number_format($r->due - $r->paid, 2) }}</td>
        </tr>
        @endforeach
    </table>
</div>

<div class="text-end"><button onclick="window.print()" class="btn btn-outline-secondary">🖨️ Print Report</button></div>
@endsection
