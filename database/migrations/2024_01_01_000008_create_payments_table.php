<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('receipt_no', 20)->unique();
            $table->foreignId('tax_id')->constrained('tax_records', 'tax_id')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties', 'property_id')->cascadeOnDelete();
            $table->foreignId('owner_id')->constrained('owners', 'owner_id')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['Cash', 'EVC Plus', 'Golis Pesa', 'Bank Transfer'])->default('Cash');
            $table->date('payment_date')->useCurrent();
            $table->foreignId('received_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->enum('status', ['Paid', 'Partial'])->default('Paid');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
