<?php
namespace App\Http\Controllers;

use App\Models\TaxRecord;
use App\Models\Owner;
use App\Models\SmsReminder;
use App\Models\ActivityLog;
use App\Services\SmsGateway;
use Illuminate\Http\Request;

class SmsReminderController extends Controller {
    public function index() {
        $logs = SmsReminder::with('owner')->orderByDesc('sms_id')->limit(50)->get();
        $owners = Owner::orderBy('full_name')->get();
       return view('sms_reminders.index', compact('logs', 'owners'));
    }

    /**
     * Send a custom reminder to ONE specific owner chosen via search.
     */
    public function sendIndividual(Request $request, SmsGateway $sms) {
        $data = $request->validate([
            'owner_id' => 'required|exists:owners,owner_id',
            'message' => 'required|string|max:500',
        ]);

        $owner = Owner::findOrFail($data['owner_id']);
        $sms->send($owner, $owner->phone_number, $data['message']);

        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'Individual SMS sent', 'details' => "To {$owner->full_name}"]);

        return redirect()->route('sms-reminders.index')->with('success', "SMS ayaa loo diray {$owner->full_name}.");
    }
}