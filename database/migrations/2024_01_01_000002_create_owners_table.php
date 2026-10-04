<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('owners', function (Blueprint $table) {
            $table->id('owner_id');
            $table->string('full_name', 100);
            $table->string('phone_number', 20);
            $table->string('national_id', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('district', 50)->default('Garowe');
            $table->date('registered_date')->useCurrent();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('owners'); }
};
