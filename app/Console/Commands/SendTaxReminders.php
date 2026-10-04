<?php
namespace App\Console\Commands;

use App\Models\TaxRecord;
use App\Models\ActivityLog;
use App\Services\SmsGateway;
use Illuminate\Console\Command;

class SendTaxReminders extends Command {
    protected $signature = 'ghtcs:send-reminders';
    protected $description = 'Send automated SMS reminders for upcoming or overdue property tax (run daily via scheduler)';

    public function handle(SmsGateway $gateway): int {
        $today = now()->toDateString();
        $daysBefore = 7;

        // Mark overdue
        TaxRecord::whereIn('status', ['Unpaid', 'Partial'])
            ->where('due_date', '<', $today)
            ->update(['status' => 'Overdue']);

        $records = TaxRecord::with('property.owner')
            ->whereIn('status', ['Unpaid', 'Partial', 'Overdue'])
            ->where(function ($q) use ($today) {
                $q->whereNull('last_reminder_date')->orWhere('last_reminder_date', '<', $today);
            })
            ->where(function ($q) use ($daysBefore) {
                $q->where('status', 'Overdue')
                  ->orWhereRaw('DATEDIFF(due_date, CURDATE()) <= ?', [$daysBefore]);
            })
            ->get();

        $sent = 0;
        foreach ($records as $tax) {
            $owner = $tax->property->owner;
            $balance = $tax->amount_due - $tax->amount_paid;

            $message = $tax->status === 'Overdue'
                ? "Salaan {$owner->full_name}, canshuurta guriga {$tax->property->house_id} waa dhaaftay taariikhda ({$tax->due_date}). Hadhaaga: $" . number_format($balance, 2) . ". Fadlan degdeg u bixi. - Degmada Garowe"
                : "Salaan {$owner->full_name}, canshuurta guriga {$tax->property->house_id} waxay dhamaanaysaa {$tax->due_date}. Hadhaaga: $" . number_format($balance, 2) . ". Fadlan bixi wakhtigeeda. - Degmada Garowe";

            $gateway->send($owner, $owner->phone_number, $message);
            $tax->update(['last_reminder_date' => $today]);
            $sent++;

            $this->line("[{$tax->property->house_id}] {$owner->full_name} ({$owner->phone_number})");
        }

        ActivityLog::create([
            'user_id' => null,
            'action' => 'Automated SMS reminders run',
            'details' => "$sent reminder(s) processed on $today",
        ]);

        $this->info("Total reminders sent: $sent");
        return self::SUCCESS;
    }
}
