<?php
namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

// Lets Administrators create/manage Neighborhoods/Villages (Xaafadaha) e.g. Wadajir, Israac...
class VillageController extends Controller {
    public function index() {
        $villages = Village::withCount('properties')->orderBy('name')->get();
        return view('villages.index', compact('villages'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:villages,name',
        ]);
        $data['district'] = 'Garowe';
        Village::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'New village/neighborhood created', 'details' => $data['name']]);
        return back()->with('success', 'Xaafadda cusub waa la keydiyay.');
    }

    public function update(Request $request, Village $village) {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:villages,name,' . $village->id,
        ]);
        $village->update($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Updated village/neighborhood', 'details' => $village->name]);
        return back()->with('success', 'Xaafadda waa la cusboonaysiiyay.');
    }

    public function destroy(Village $village) {
        if ($village->properties()->exists()) {
            return back()->with('error', 'Xaafaddan lama tirtiri karo — waxaa jira guryo isticmaalaya.');
        }
        $village->delete();
        return back()->with('success', 'Xaafadda waa la tirtiray.');
    }
}
