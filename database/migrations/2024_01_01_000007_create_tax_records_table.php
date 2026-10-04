<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_records', function (Blueprint $table) {
            $table->id('tax_id');
            $table->foreignId('property_id')->constrained('properties', 'property_id')->cascadeOnDelete();
            $table->year('tax_year');
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('due_date');
            $table->enum('status', ['Paid', 'Partial', 'Unpaid', 'Overdue'])->default('Unpaid');
            $table->date('last_reminder_date')->nullable();
            $table->timestamps();
            $table->unique(['property_id', 'tax_year']);
        });
    }
    public function down(): void { Schema::dropIfExists('tax_records'); }
};
