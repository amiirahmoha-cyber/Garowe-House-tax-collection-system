@extends('layouts.app')
@section('title', 'SMS Reminders - GHTCS')
@section('content')
<div class="page-header"><h1>SMS Reminders</h1><p>Fariimaha canshuurta waxaa si otomaatig ah loo diraa marka tax-ka la generate-gareeyo. Halkan waxaad ka diri kartaa fariin gaar ah oo qof la doonayo.</p></div>

<div class="panel" style="max-width:600px;">
    <div class="panel-header"><h3>Send Individual Reminder</h3></div>
    <p style="font-size:13.5px; color:var(--text-muted);">Dooro qof gaar ah oo aad u dirto fariin khaas ah.</p>
    <form method="POST" action="{{ route('sms-reminders.send-individual') }}">
        @csrf
        <label class="form-label">Select Owner *</label>
        <select name="owner_id" class="form-control" required>
            <option value="">-- Select Owner --</option>
            @foreach($owners as $o)
                <option value="{{ $o->owner_id }}">{{ $o->full_name }} ({{ $o->phone_number }})</option>
            @endforeach
        </select>

        <label class="form-label">Message *</label>
        <textarea name="message" class="form-control" rows="4" placeholder="Qor fariinta gaarka ah halkan..." required></textarea>
        <button type="submit" class="btn btn-primary">✉️ Send to This Owner</button>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h3>Recent SMS Log</h3></div>
    <table class="data-table">
        <tr><th>Owner</th><th>Phone</th><th>Message</th><th>Sent</th><th>Status</th></tr>
        @foreach($logs as $l)
        <tr>
            <td>{{ $l->owner->full_name ?? '—' }}</td>
            <td>{{ $l->phone_number }}</td>
            <td style="max-width:320px; white-space:normal; font-size:12.5px; color:#475569;">{{ $l->message }}</td>
            <td>{{ \Carbon\Carbon::parse($l->sent_date)->format('M j, g:ia') }}</td>
            <td><span class="badge badge-paid">{{ $l->status }}</span></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection