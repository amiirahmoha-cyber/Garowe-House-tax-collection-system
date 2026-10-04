<?php
namespace App\Http\Controllers;

use App\Models\PropertyClass;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

// Lets Administrators create/manage new Property Classes (e.g. Class A, B, C, or custom names)
// with their own rate-per-square-meter, the same way Owners are registered.
class PropertyClassController extends Controller {
    public function index() {
        $classes = PropertyClass::withCount('properties')->orderBy('name')->get();
        return view('property_classes.index', compact('classes'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:property_classes,name',
            'rate_per_sqm' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:150',
        ]);
        PropertyClass::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'New property class created', 'details' => $data['name']]);
        return back()->with('success', 'Class-ka cusub waa la keydiyay.');
    }

    public function update(Request $request, PropertyClass $property_class) {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:property_classes,name,' . $property_class->id,
            'rate_per_sqm' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:150',
        ]);
        $property_class->update($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Updated property class', 'details' => $property_class->name]);
        return back()->with('success', 'Class-ka waa la cusboonaysiiyay.');
    }

    public function destroy(PropertyClass $property_class) {
        if ($property_class->properties()->exists()) {
            return back()->with('error', 'Class-kan lama tirtiri karo — waxaa jira guryo isticmaalaya.');
        }
        $property_class->delete();
        return back()->with('success', 'Class-ka waa la tirtiray.');
    }
}
