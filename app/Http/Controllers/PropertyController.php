<?php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Owner;
use App\Models\PropertyType;
use App\Models\PropertyClass;
use App\Models\Village;
use App\Models\TaxRecord;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PropertyController extends Controller {
    public function index(Request $request) {
        $search = trim($request->get('q', ''));
        $properties = Property::with(['owner', 'propertyType', 'propertyClass'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('house_id', 'like', "%$search%")
                  ->orWhereHas('owner', fn($o) => $o->where('full_name', 'like', "%$search%"));
            })
            ->orderByDesc('property_id')->get();

        return view('properties.index', compact('properties', 'search'));
    }

    public function create() {
        $owners = Owner::where('status', 'Active')->orderBy('full_name')->get();
        $types = PropertyType::orderBy('name')->get();
        $classes = PropertyClass::orderBy('name')->get();
        $villages = Village::orderBy('name')->get();
        return view('properties.create', compact('owners', 'types', 'classes', 'villages'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'owner_id' => 'required|exists:owners,owner_id',
            'property_type_id' => 'required|exists:property_types,id',
            'property_class_id' => 'required|exists:property_classes,id',
            'village_id' => 'nullable|exists:villages,id',
            'rooms' => 'required|integer|min:1',
            'square_meters' => 'required|numeric|min:0.01',
            'address' => 'nullable|string|max:150',
        ]);
        $data['district'] = 'Garowe';

        $type = PropertyType::findOrFail($data['property_type_id']);
        $class = PropertyClass::findOrFail($data['property_class_id']);

        $data['house_id'] = Property::generateHouseId($type);
        // Tax amount is READ LIVE from the house size (square_meters) x class rate
        $data['annual_tax'] = round($data['square_meters'] * $class->rate_per_sqm, 2);

        $property = Property::create($data);

        // Auto-create this year's tax record
        $year = date('Y');
        TaxRecord::create([
            'property_id' => $property->property_id,
            'tax_year' => $year,
            'amount_due' => $property->annual_tax,
            'due_date' => "$year-12-31",
            'status' => 'Unpaid',
        ]);

        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'New property registered', 'details' => $property->house_id]);

        return redirect()->route('properties.index')->with('success', 'Property-ga waa la keydiyay.');
    }

    public function show(Property $property) {
        $property->load(['owner', 'propertyType', 'propertyClass', 'village']);
        $taxRecords = $property->taxRecords()->orderByDesc('tax_year')->get();
        $payments = $property->payments()->orderByDesc('payment_id')->get();
        return view('properties.show', compact('property', 'taxRecords', 'payments'));
    }

    public function edit(Property $property) {
        $owners = Owner::orderBy('full_name')->get();
        $types = PropertyType::orderBy('name')->get();
        $classes = PropertyClass::orderBy('name')->get();
        $villages = Village::orderBy('name')->get();
        return view('properties.edit', compact('property', 'owners', 'types', 'classes', 'villages'));
    }

    public function update(Request $request, Property $property) {
        $data = $request->validate([
            'owner_id' => 'required|exists:owners,owner_id',
            'property_type_id' => 'required|exists:property_types,id',
            'property_class_id' => 'required|exists:property_classes,id',
            'village_id' => 'nullable|exists:villages,id',
            'rooms' => 'required|integer|min:1',
            'square_meters' => 'required|numeric|min:0.01',
            'address' => 'nullable|string|max:150',
            'status' => 'required|in:Active,Inactive',
        ]);

        $class = PropertyClass::findOrFail($data['property_class_id']);
        // Recalculate tax live from the (possibly updated) house size
        $data['annual_tax'] = round($data['square_meters'] * $class->rate_per_sqm, 2);

        $property->update($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Updated property', 'details' => $property->house_id]);

        return redirect()->route('properties.index')->with('success', 'Property-ga waa la cusboonaysiiyay.');
    }

    public function destroy(Property $property) {
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Deleted property', 'details' => $property->house_id]);
        $property->delete();
        return redirect()->route('properties.index')->with('success', 'Property-ga waa la tirtiray.');
    }
}
