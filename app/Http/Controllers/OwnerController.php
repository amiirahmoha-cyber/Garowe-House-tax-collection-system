<?php
namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class OwnerController extends Controller {
    public function index(Request $request) {
        $search = trim($request->get('q', ''));
        $owners = Owner::withCount('properties')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            })
            ->orderByDesc('owner_id')->get();

        return view('owners.index', compact('owners', 'search'));
    }

    public function create() {
        return view('owners.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:150',
        ]);
        $data['district'] = 'Garowe';

        $owner = Owner::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'New owner registered', 'details' => $owner->full_name]);

        return redirect()->route('owners.index')->with('success', 'Owner-ka waa la keydiyay.');
    }

    public function show(Owner $owner) {
        $owner->load(['properties.propertyType', 'properties.propertyClass']);
        $payments = $owner->payments()->with('property')->latest('payment_id')->limit(10)->get();
        $totalDue = $owner->properties->flatMap->taxRecords
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->sum(fn($t) => $t->amount_due - $t->amount_paid);
        $totalPaid = $owner->payments()->sum('amount');

        return view('owners.show', compact('owner', 'payments', 'totalDue', 'totalPaid'));
    }

    public function edit(Owner $owner) {
        return view('owners.edit', compact('owner'));
    }

    public function update(Request $request, Owner $owner) {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:150',
            'status' => 'required|in:Active,Inactive',
        ]);
        $owner->update($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Updated owner', 'details' => $owner->full_name]);

        return redirect()->route('owners.index')->with('success', 'Owner-ka waa la cusboonaysiiyay.');
    }

    public function destroy(Owner $owner) {
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Deleted owner', 'details' => "Owner ID: {$owner->owner_id}"]);
        $owner->delete();
        return redirect()->route('owners.index')->with('success', 'Owner-ka waa la tirtiray.');
    }
}
