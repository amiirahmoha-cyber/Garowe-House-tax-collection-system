<?php
namespace App\Http\Controllers;

use App\Models\PropertyType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

// Lets Administrators create/manage Property Types (Residential, Commercial, or custom)
class PropertyTypeController extends Controller {
    public function index() {
        $types = PropertyType::withCount('properties')->orderBy('name')->get();
        return view('property_types.index', compact('types'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:property_types,name',
            'description' => 'nullable|string|max:150',
        ]);
        PropertyType::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'New property type created', 'details' => $data['name']]);
        return back()->with('success', 'Type-ka cusub waa la keydiyay.');
    }

    public function update(Request $request, PropertyType $property_type) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:property_types,name,' . $property_type->id,
            'description' => 'nullable|string|max:150',
        ]);
        $property_type->update($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Updated property type', 'details' => $property_type->name]);
        return back()->with('success', 'Type-ka waa la cusboonaysiiyay.');
    }

    public function destroy(PropertyType $property_type) {
        if ($property_type->properties()->exists()) {
            return back()->with('error', 'Type-kan lama tirtiri karo — waxaa jira guryo isticmaalaya.');
        }
        $property_type->delete();
        return back()->with('success', 'Type-ka waa la tirtiray.');
    }
}
