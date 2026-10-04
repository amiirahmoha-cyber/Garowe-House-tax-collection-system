@extends('layouts.app')
@section('title', 'Property Owners - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Property Owners</h1><p>Manage all registered property owners in Garowe district.</p></div>
    <a href="{{ route('owners.create') }}" class="btn btn-primary">➕ Add Owner</a>
</div>

<div class="panel">
    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width:300px; margin-bottom:0;" placeholder="Search by name or phone..." value="{{ $search }}">
        <button class="btn btn-outline-secondary">Search</button>
    </form>

    <table class="data-table">
        <tr><th>#</th><th>Full Name</th><th>Phone Number</th><th>District</th><th>Properties</th><th>Registered</th><th>Status</th><th>Actions</th></tr>
        @foreach($owners as $i => $row)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-weight:700; color:var(--navy);">{{ $row->full_name }}</td>
            <td>{{ $row->phone_number }}</td>
            <td>{{ $row->district }}</td>
            <td>{{ $row->properties_count }}</td>
            <td>{{ \Carbon\Carbon::parse($row->registered_date)->format('M j, Y') }}</td>
            <td><span class="badge {{ $row->status=='Active' ? 'badge-paid':'badge-unpaid' }}">{{ $row->status }}</span></td>
            <td>
                <a href="{{ route('owners.show', $row) }}" class="action-btn btn-view">View</a>
                <a href="{{ route('owners.edit', $row) }}" class="action-btn btn-edit">Edit</a>
                <form action="{{ route('owners.destroy', $row) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ma hubtaa inaad tirtirayso owner-kan?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn btn-delete">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
