@extends('layouts.app')
@section('title', 'Users - GHTCS')
@section('content')
<div class="page-header"><h1>System Users</h1><p>Manage Administrator, Collector, and Viewer accounts.</p></div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-header"><h3>All Users</h3></div>
        <table class="data-table">
            <tr><th>Name</th><th>Username</th><th>Role</th><th>Status</th></tr>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->full_name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->role }}</td>
                <td><span class="badge {{ $u->status=='Active'?'badge-paid':'badge-unpaid' }}">{{ $u->status }}</span></td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="panel">
        <div class="panel-header"><h3>Add New User</h3></div>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="Collector">Collector (Payments/Properties/Owners only)</option>
                <option value="Administrator">Administrator (full access)</option>
                <option value="Viewer">Viewer (Payments/Properties/Owners only)</option>
            </select>
            <button type="submit" class="btn btn-primary mt-2">Add User</button>
        </form>
    </div>
</div>
@endsection
