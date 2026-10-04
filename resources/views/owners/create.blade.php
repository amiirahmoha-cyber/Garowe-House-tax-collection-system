@extends('layouts.app')
@section('title', 'Add Owner - GHTCS')
@section('content')
<div class="page-header"><h1>Register Property Owner</h1><p>Add a new property owner to the system.</p></div>

<div class="form-card" style="max-width:600px;">
    <div class="district-lock">🔒 District: <strong>Garowe</strong> (fixed for this system)</div>
    <form method="POST" action="{{ route('owners.store') }}">
        @csrf
        <label class="form-label">Full Name *</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>

        <label class="form-label">Phone Number *</label>
        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="e.g. 0907xxxxxx" required>

        <label class="form-label">National ID</label>
        <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">

        <label class="form-label">Address / Neighborhood</label>
        <input type="text" name="address" class="form-control" value="{{ old('address') }}">

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Save Owner</button>
            <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
