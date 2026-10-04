<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sms_reminders', function (Blueprint $table) {
            $table->id('sms_id');
            $table->foreignId('owner_id')->constrained('owners', 'owner_id')->cascadeOnDelete();
            $table->string('phone_number', 20);
            $table->text('message');
            $table->dateTime('sent_date')->useCurrent();
            $table->enum('status', ['Sent', 'Failed', 'Pending'])->default('Sent');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sms_reminders'); }
};
