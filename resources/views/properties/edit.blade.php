@extends('layouts.app')
@section('title', 'Edit Property - GHTCS')
@section('content')
<div class="page-header"><h1>Edit Property — {{ $property->house_id }}</h1></div>
<div class="form-card" style="max-width:650px;">
    <form method="POST" action="{{ route('properties.update', $property) }}">
        @csrf @method('PUT')
        <label class="form-label">Property Owner *</label>
        <div class="searchable-select">
            <input type="text" class="form-control" id="owner_search" value="{{ $property->owner->full_name }} ({{ $property->owner->phone_number }})" placeholder="Ku qor magaca ama taleefanka si aad u raadiso..." autocomplete="off">
            <input type="hidden" name="owner_id" id="owner_id" value="{{ $property->owner_id }}" required>
            <div id="owner_results" class="search-results"></div>
        </div>

        <div class="row">
            <div class="col-6">
                <label class="form-label">Property Type *</label>
                <select name="property_type_id" class="form-control" required>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}" {{ $t->id==$property->property_type_id?'selected':'' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Property Class *</label>
                <select name="property_class_id" class="form-control" required>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $c->id==$property->property_class_id?'selected':'' }}>{{ $c->name }} (${{ number_format($c->rate_per_sqm,2) }}/m²)</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <label class="form-label">Number of Rooms *</label>
                <input type="number" name="rooms" class="form-control" value="{{ $property->rooms }}" min="1" required>
            </div>
            <div class="col-6">
                <label class="form-label">Size (Square Meters) *</label>
                <input type="number" step="0.01" name="square_meters" class="form-control" value="{{ $property->square_meters }}" min="0.01" required>
            </div>
        </div>

        <label class="form-label">Village / Xaafad (Garowe)</label>
        <select name="village_id" class="form-control">
            <option value="">-- Select Neighborhood --</option>
            @foreach($villages as $v)
                <option value="{{ $v->id }}" {{ $v->id==$property->village_id?'selected':'' }}>{{ $v->name }}</option>
            @endforeach
        </select>

        <label class="form-label">Address / Details</label>
        <input type="text" name="address" class="form-control" value="{{ $property->address }}">

        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="Active" {{ $property->status=='Active'?'selected':'' }}>Active</option>
            <option value="Inactive" {{ $property->status=='Inactive'?'selected':'' }}>Inactive</option>
        </select>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Update Property</button>
            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const ownersData = @json($owners->map(fn($o) => ['id' => $o->owner_id, 'label' => $o->full_name . ' (' . $o->phone_number . ')']));
const searchInput = document.getElementById('owner_search');
const hiddenInput = document.getElementById('owner_id');
const resultsBox = document.getElementById('owner_results');

searchInput.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    hiddenInput.value = '';
    resultsBox.innerHTML = '';
    if (q === '') { resultsBox.style.display = 'none'; return; }

    const matches = ownersData.filter(o => o.label.toLowerCase().includes(q));
    if (matches.length === 0) {
        resultsBox.innerHTML = '<div class="search-result-item text-muted">Lama helin</div>';
    } else {
        matches.slice(0, 20).forEach(o => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.textContent = o.label;
            item.onclick = () => {
                searchInput.value = o.label;
                hiddenInput.value = o.id;
                resultsBox.style.display = 'none';
            };
            resultsBox.appendChild(item);
        });
    }
    resultsBox.style.display = 'block';
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.searchable-select')) resultsBox.style.display = 'none';
});
</script>
@endsection
