@extends('layouts.app')
@section('title', 'Dashboard - GHTCS')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="page-header">
    <h1>Welcome back, {{ auth()->user()->full_name }}! 👋</h1>
    <p>Here's what's happening with the house tax collection system today.</p>
</div>

<div class="stats-row">
    <div class="stat-card">
        <div class="icon bg-blue">🏠</div>
        <div class="label">Total Properties</div>
        <div class="value">{{ $totalProperties }}</div>
        <div class="sub">All registered properties</div>
        <a href="{{ route('properties.index') }}" class="link">View all →</a>
    </div>
    <div class="stat-card">
        <div class="icon bg-green">👥</div>
        <div class="label">Total Owners</div>
        <div class="value">{{ $totalOwners }}</div>
        <div class="sub">Registered property owners</div>
        <a href="{{ route('owners.index') }}" class="link">View all →</a>
    </div>
    <div class="stat-card">
        <div class="icon bg-purple">💰</div>
        <div class="label">Total Collected</div>
        <div class="value">${{ number_format($totalCollected, 2) }}</div>
        <div class="sub">This year collection</div>
        <a href="{{ route('payments.index') }}" class="link">View report →</a>
    </div>
    <div class="stat-card">
        <div class="icon bg-orange">⏳</div>
        <div class="label">Pending Taxes</div>
        <div class="value">${{ number_format($pendingTaxes, 2) }}</div>
        <div class="sub">Not yet paid</div>
        @if(auth()->user()->isAdmin())<a href="{{ route('tax-records.index') }}" class="link">View details →</a>@endif
    </div>
    <div class="stat-card">
        <div class="icon bg-red">⚠️</div>
        <div class="label">Overdue Taxes</div>
        <div class="value">${{ number_format($overdueTaxes, 2) }}</div>
        <div class="sub">Past due date</div>
        @if(auth()->user()->isAdmin())<a href="{{ route('tax-records.index') }}" class="link">View details →</a>@endif
    </div>
    <div class="stat-card">
        <div class="icon bg-teal">💬</div>
        <div class="label">SMS Sent Today</div>
        <div class="value">{{ $smsToday }}</div>
        <div class="sub">Messages sent today</div>
        @if(auth()->user()->isAdmin())<a href="{{ route('sms-reminders.index') }}" class="link">View all →</a>@endif
    </div>
</div>

<div class="grid-3">
    <div class="panel">
        <div class="panel-header"><h3>Collection by Month ({{ $year }})</h3></div>
        <canvas id="monthlyChart" height="110"></canvas>
    </div>
    <div class="panel">
        <div class="panel-header"><h3>Payment Status ({{ $year }})</h3></div>
        <canvas id="statusChart" height="140"></canvas>
    </div>
    <div class="panel">
        <div class="panel-header"><h3>Collection by Property Class</h3></div>
        <canvas id="typeChart" height="140"></canvas>
    </div>
</div>

<div class="grid-3" style="grid-template-columns:1fr 1.3fr 1.3fr;">
    <div class="panel">
        <div class="panel-header"><h3>Quick Actions</h3></div>
        <div class="quick-actions">
            <a href="{{ route('owners.create') }}" class="qa-blue">➕ Add Owner</a>
            <a href="{{ route('properties.create') }}" class="qa-green">🏠 Register Property</a>
            <a href="{{ route('payments.create') }}" class="qa-orange">📇 Record Payment</a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('sms-reminders.index') }}" class="qa-purple">📨 Send SMS Reminder</a>
            <a href="{{ route('reports.index') }}" class="qa-red">📄 Generate Report</a>
            <a href="{{ route('reports.index') }}" class="qa-teal">📊 View Reports</a>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Recent Payments</h3><a href="{{ route('payments.index') }}">View all →</a></div>
        <table class="data-table">
            <tr><th>Receipt No</th><th>Property No</th><th>Owner</th><th>Amount</th><th>Status</th><th></th></tr>
            @foreach($recentPayments as $p)
            <tr>
                <td>{{ $p->receipt_no }}</td>
                <td style="font-weight:700; color:var(--navy);">{{ $p->property->house_id }}</td>
                <td>{{ $p->owner->full_name }}</td>
                <td>${{ number_format($p->amount, 2) }}</td>
                <td><span class="badge badge-{{ strtolower($p->status) }}">{{ $p->status }}</span></td>
                <td><a href="{{ route('receipts.show', $p->payment_id) }}">🖨️</a></td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Top Unpaid Properties</h3>@if(auth()->user()->isAdmin())<a href="{{ route('tax-records.index') }}">View all →</a>@endif</div>
        <table class="data-table">
            <tr><th>Property No</th><th>Owner</th><th>Amount Due</th></tr>
            @foreach($topUnpaid as $t)
            <tr>
                <td style="font-weight:700; color:var(--navy);">{{ $t->property->house_id }}</td>
                <td>{{ $t->property->owner->full_name }}</td>
                <td style="color:#dc2626; font-weight:700;">${{ number_format($t->amount_due - $t->amount_paid, 2) }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>

@if(auth()->user()->isAdmin())
<div class="panel">
    <div class="panel-header"><h3>Recent Activity</h3></div>
    @foreach($activities as $a)
    <div class="activity-item">
        <div class="activity-icon bg-blue">•</div>
        <div>
            <div class="activity-title">{{ $a->action }}</div>
            <div class="activity-sub">{{ $a->details }}</div>
        </div>
        <div class="activity-time">{{ $a->created_at->format('M j, g:ia') }}</div>
    </div>
    @endforeach
</div>
@endif

<script>
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{ label: 'Collected Amount', data: @json(array_values($monthly)), backgroundColor: '#2563eb', borderRadius: 4 }]
    },
    options: { plugins:{legend:{position:'bottom'}}, scales:{y:{beginAtZero:true}} }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: ['Paid','Partial','Unpaid'], datasets: [{ data: [{{ $paidSum }}, {{ $partialSum }}, {{ $unpaidSum }}], backgroundColor: ['#16a34a','#f59e0b','#dc2626'] }] },
    options: { plugins:{legend:{position:'right'}} }
});

new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: @json($classLabels),
        datasets: [{
            data: @json($classSums),
            backgroundColor: ['#2563eb','#16a34a','#f59e0b','#7c3aed','#dc2626','#0d9488']
        }]
    },
    options: { plugins:{legend:{position:'right'}} }
});

</script>
@endsection
