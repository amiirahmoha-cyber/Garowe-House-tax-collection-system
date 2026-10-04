@extends('layouts.app')
@section('title', 'Properties - GHTCS')
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Properties</h1><p>All registered houses under Garowe district.</p></div>
    <a href="{{ route('properties.create') }}" class="btn btn-primary">➕ Register Property</a>
</div>

<div class="panel">
    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width:300px; margin-bottom:0;" placeholder="Search House ID or Owner..." value="{{ $search }}">
        <button class="btn btn-outline-secondary">Search</button>
    </form>

    <table class="data-table">
        <tr><th>House ID</th><th>Owner</th><th>Type</th><th>Class</th><th>Rooms</th><th>Size (m²)</th><th>District</th><th>Annual Tax</th><th>Actions</th></tr>
        @foreach($properties as $row)
        <tr>
            <td style="font-weight:700; color:var(--navy);">{{ $row->house_id }}</td>
            <td>{{ $row->owner->full_name }}</td>
            <td>{{ $row->propertyType->name }}</td>
            <td>{{ $row->propertyClass->name }}</td>
            <td>{{ $row->rooms }}</td>
            <td>{{ number_format($row->square_meters, 1) }}</td>
            <td>{{ $row->district }}</td>
            <td>${{ number_format($row->annual_tax, 2) }}</td>
            <td>
                <a href="{{ route('properties.show', $row) }}" class="action-btn btn-view">View</a>
                <a href="{{ route('properties.edit', $row) }}" class="action-btn btn-edit">Edit</a>
                <form action="{{ route('properties.destroy', $row) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ma hubtaa inaad tirtirayso guriga?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection