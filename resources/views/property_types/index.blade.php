@extends('layouts.app')
@section('title', 'Property Types - GHTCS')
@section('content')
<div class="page-header"><h1>Property Types</h1><p>Samee nooc cusub oo guriga (tusaale: Residential, Commercial, Industrial).</p></div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>All Types</h3></div>
        <table class="data-table">
            <tr><th>Name</th><th>Properties</th><th></th></tr>
            @foreach($types as $t)
            <tr>
                <td style="font-weight:700; color:var(--navy);">{{ $t->name }}</td>
                <td>{{ $t->properties_count }}</td>
                <td>
                    <form action="{{ route('property-types.destroy', $t) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ma hubtaa?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Add New Type</h3></div>
        <form method="POST" action="{{ route('property-types.store') }}">
            @csrf
            <label class="form-label">Type Name *</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Industrial" required>
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control">
            <button type="submit" class="btn btn-primary mt-2">Save Type</button>
        </form>
    </div>
</div>
@endsection
