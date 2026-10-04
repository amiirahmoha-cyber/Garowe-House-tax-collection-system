@extends('layouts.app')
@section('title', 'Activity Logs - GHTCS')
@section('content')
<div class="page-header"><h1>Activity Logs</h1><p>System audit trail of all user actions.</p></div>
<div class="panel">
    <table class="data-table">
        <tr><th>User</th><th>Action</th><th>Details</th><th>Time</th></tr>
        @foreach($logs as $l)
        <tr>
            <td>{{ $l->user->full_name ?? 'System' }}</td>
            <td>{{ $l->action }}</td>
            <td>{{ $l->details }}</td>
            <td>{{ $l->created_at->format('M j, Y g:ia') }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
