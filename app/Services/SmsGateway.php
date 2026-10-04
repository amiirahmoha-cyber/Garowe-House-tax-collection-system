<?php
namespace App\Services;

use App\Models\Owner;
use App\Models\SmsReminder;
use Illuminate\Support\Facades\Http;

/**
 * Generic SMS Gateway wrapper.
 *
 * Garowe/Puntland's main carrier is GOLIS TELECOM, which does not publish a
 * self-service developer API. Two real options:
 *
 *   1. Contact Golis Telecom directly (Bosaso, Biyo Kulule Road /
 *      https://golistelecom.com/contact-us/) and request their
 *      Corporate/Business Bulk SMS API.
 *   2. Use a third-party aggregator that already connects to Somali
 *      networks (Golis included), e.g. D7 Networks (d7networks.com) or
 *      SMS.to — both offer instant self-service REST API sign-up.
 *
 * Whichever you choose, set the credentials in .env (see .env.example)
 * and this class will call it. Until SMS_ENABLED=true, messages are
 * logged locally as "Sent" (simulation mode) so the app is fully
 * testable without a live gateway.
 */
class SmsGateway {
    public function send(Owner $owner, string $phone, string $message): bool {
        $status = 'Sent';

        if (config('sms.enabled')) {
            $mobile = $this->normalize($phone);
            $status = $this->callGateway($mobile, $message) ? 'Sent' : 'Failed';
        }

        SmsReminder::create([
            'owner_id' => $owner->owner_id,
            'phone_number' => $phone,
            'message' => $message,
            'status' => $status,
        ]);

        return $status === 'Sent';
    }

    protected function normalize(string $phone): string {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '252')) $phone = substr($phone, 3);
        elseif (str_starts_with($phone, '0')) $phone = substr($phone, 1);
        return $phone;
    }

    protected function callGateway(string $mobile, string $message): bool {
        try {
            $response = Http::asForm()->timeout(15)->post(config('sms.url'), [
                'username' => config('sms.username'),
                'password' => config('sms.password'),
                'senderid' => config('sms.sender_id'),
                'mobile'   => $mobile,
                'message'  => $message,
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
