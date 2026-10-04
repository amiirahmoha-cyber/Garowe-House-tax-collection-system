@extends('layouts.app')
@section('title', 'Edit Owner - GHTCS')
@section('content')
<div class="page-header"><h1>Edit Property Owner</h1></div>
<div class="form-card" style="max-width:600px;">
    <form method="POST" action="{{ route('owners.update', $owner) }}">
        @csrf @method('PUT')
        <label class="form-label">Full Name *</label>
        <input type="text" name="full_name" class="form-control" value="{{ $owner->full_name }}" required>

        <label class="form-label">Phone Number *</label>
        <input type="text" name="phone_number" class="form-control" value="{{ $owner->phone_number }}" required>

        <label class="form-label">National ID</label>
        <input type="text" name="national_id" class="form-control" value="{{ $owner->national_id }}">

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $owner->email }}">

        <label class="form-label">Address / Neighborhood</label>
        <input type="text" name="address" class="form-control" value="{{ $owner->address }}">

        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="Active" {{ $owner->status=='Active'?'selected':'' }}>Active</option>
            <option value="Inactive" {{ $owner->status=='Inactive'?'selected':'' }}>Inactive</option>
        </select>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Update Owner</button>
            <a href="{{ route('owners.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
