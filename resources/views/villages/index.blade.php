@extends('layouts.app')
@section('title', 'Villages - GHTCS')
@section('content')
<div class="page-header"><h1>Villages / Xaafadaha</h1><p>Samee xaafad cusub oo Garowe ah (sida Wadajir, Israac, iwm).</p></div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>All Villages</h3></div>
        <table class="data-table">
            <tr><th>Name</th><th>District</th><th>Properties</th><th></th></tr>
            @foreach($villages as $v)
            <tr>
                <td style="font-weight:700; color:var(--navy);">{{ $v->name }}</td>
                <td>{{ $v->district }}</td>
                <td>{{ $v->properties_count }}</td>
                <td>
                    <form action="{{ route('villages.destroy', $v) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ma hubtaa?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Add New Village / Xaafad</h3></div>
        <form method="POST" action="{{ route('villages.store') }}">
            @csrf
            <label class="form-label">Village / Neighborhood Name *</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Xamar-jajab" required>
            <button type="submit" class="btn btn-primary mt-2">Save Village</button>
        </form>
    </div>
</div>
@endsection
