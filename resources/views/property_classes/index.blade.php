@extends('layouts.app')
@section('title', 'Property Classes - GHTCS')
@section('content')
<div class="page-header"><h1>Property Classes</h1><p>Samee class cusub oo canshuur gaar ah leh (per square meter).</p></div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>All Classes</h3></div>
        <table class="data-table">
            <tr><th>Name</th><th>Rate / m²</th><th>Properties</th><th></th></tr>
            @foreach($classes as $c)
            <tr>
                <td style="font-weight:700; color:var(--navy);">{{ $c->name }}</td>
                <td>${{ number_format($c->rate_per_sqm, 2) }}</td>
                <td>{{ $c->properties_count }}</td>
                <td>
                    <form action="{{ route('property-classes.destroy', $c) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ma hubtaa?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Add New Class</h3></div>
        <form method="POST" action="{{ route('property-classes.store') }}">
            @csrf
            <label class="form-label">Class Name *</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Class D" required>
            <label class="form-label">Rate per Square Meter ($) *</label>
            <input type="number" step="0.01" name="rate_per_sqm" class="form-control" required>
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control">
            <button type="submit" class="btn btn-primary mt-2">Save Class</button>
        </form>
    </div>
</div>
@endsection
