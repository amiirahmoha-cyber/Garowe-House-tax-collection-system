<!DOCTYPE html>
<html lang="so">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'GHTCS')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
@stack('head')
</head>
<body>
<div class="app-wrapper">
    <div class="sidebar">
        <div class="brand">
            <img src="{{ asset('img/logo.png') }}" onerror="this.style.display='none'" alt="Logo">
            <span>GAROWE HOUSE TAX<br>COLLECTION SYSTEM</span>
        </div>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">🏠 Dashboard</a>

        <div class="nav-section-title">Management</div>
        <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}">👤 Property Owners</a>
        <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">🏘️ Properties</a>
        <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') || request()->routeIs('receipts.*') ? 'active' : '' }}">💳 Payments</a>

        @if(auth()->user() && auth()->user()->isAdmin())
        <a href="{{ route('tax-records.index') }}" class="nav-link {{ request()->routeIs('tax-records.*') ? 'active' : '' }}">🧾 Tax Records</a>

        <div class="nav-section-title">Reports</div>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">📊 Reports</a>
        <a href="{{ route('sms-reminders.index') }}" class="nav-link {{ request()->routeIs('sms-reminders.*') ? 'active' : '' }}">✉️ SMS Reminders</a>

        <div class="nav-section-title">Settings</div>
        <a href="{{ route('property-classes.index') }}" class="nav-link {{ request()->routeIs('property-classes.*') ? 'active' : '' }}">🏷️ Property Classes</a>
        <a href="{{ route('property-types.index') }}" class="nav-link {{ request()->routeIs('property-types.*') ? 'active' : '' }}">🏗️ Property Types</a>
        <a href="{{ route('villages.index') }}" class="nav-link {{ request()->routeIs('villages.*') ? 'active' : '' }}">📍 Villages / Xaafado</a>

        <div class="nav-section-title">System</div>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">👥 Users</a>
        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">🕒 Activity Logs</a>
        @endif

        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
            @csrf
            <button type="submit" class="nav-link logout" style="background:none; border:none; width:100%; text-align:left;">🚪 Logout</button>
        </form>

        <div class="footer-card">
            <strong style="color:#fff;">Garowe Municipality</strong><br>
            Building a better future for our community.
        </div>
    </div>

    <div class="main-content">
        <div class="topbar" style="justify-content:flex-end;">
            
            <div class="top-right">
                <div style="font-size:13px; color:#475569;">
                    📅 {{ now()->format('M j, Y') }}<br>
                    <span style="font-size:11.5px; color:#94a3b8;">{{ now()->format('l') }}</span>
                </div>
                <div>🔔</div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <div style="font-size:13px;">
                        <strong>{{ auth()->user()->full_name ?? 'Guest' }}</strong><br>
                        <span style="color:#94a3b8; font-size:11.5px;">{{ auth()->user()->role ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
