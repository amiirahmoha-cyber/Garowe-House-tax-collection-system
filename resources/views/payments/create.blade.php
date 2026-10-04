@extends('layouts.app')
@section('title', 'Record Payment - GHTCS')
@section('content')
<div class="page-header"><h1>Record Payment</h1><p>Ku qor magaca milkiilaha ama House ID-ga si aad u raadiso. Receipt number waxaa si otomaatig ah loo abuuri doonaa.</p></div>

<div class="form-card" style="max-width:600px;">
    <form method="POST" action="{{ route('payments.store') }}">
        @csrf
        <label class="form-label">Search Property (Owner Name or House ID) *</label>
        <div class="searchable-select">
            <input type="text" class="form-control" id="tax_search" placeholder="Ku qor magaca ama House ID-ga..." autocomplete="off">
            <input type="hidden" name="tax_id" id="tax_id" value="{{ $preselectTaxId ?: '' }}" required>
            <div id="tax_results" class="search-results"></div>
        </div>

        <label class="form-label">Amount Paid ($) *</label>
        <input type="number" step="0.01" name="amount" class="form-control" min="0.01" required>

        <label class="form-label">Payment Method *</label>
        <select name="payment_method" class="form-control" required>
            <option value="Cash">Cash</option>
            <option value="EVC Plus">EVC Plus</option>
            <option value="Golis Pesa">Golis Pesa</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>

        <div class="d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-primary">Save Payment</button>
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const taxData = @json($unpaid->map(fn($t) => [
    'id' => $t->tax_id,
    'label' => $t->property->house_id . ' — ' . $t->property->owner->full_name . ' (Balance: $' . number_format($t->amount_due - $t->amount_paid, 2) . ')'
]));
const preselectId = {{ $preselectTaxId ?: 'null' }};
const searchInput = document.getElementById('tax_search');
const hiddenInput = document.getElementById('tax_id');
const resultsBox = document.getElementById('tax_results');

if (preselectId) {
    const match = taxData.find(t => t.id === preselectId);
    if (match) searchInput.value = match.label;
}

searchInput.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    hiddenInput.value = '';
    resultsBox.innerHTML = '';
    if (q === '') { resultsBox.style.display = 'none'; return; }

    const matches = taxData.filter(t => t.label.toLowerCase().includes(q));
    if (matches.length === 0) {
        resultsBox.innerHTML = '<div class="search-result-item text-muted">Lama helin</div>';
    } else {
        matches.slice(0, 20).forEach(t => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.textContent = t.label;
            item.onclick = () => {
                searchInput.value = t.label;
                hiddenInput.value = t.id;
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