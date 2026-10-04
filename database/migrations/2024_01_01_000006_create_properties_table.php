<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('properties', function (Blueprint $table) {
            $table->id('property_id');
            $table->string('house_id', 20)->unique();
            $table->foreignId('owner_id')->constrained('owners', 'owner_id')->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained('property_types')->restrictOnDelete();
            $table->foreignId('property_class_id')->constrained('property_classes')->restrictOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->integer('rooms')->default(1);
            $table->decimal('square_meters', 10, 2)->default(0);
            $table->string('district', 50)->default('Garowe');
            $table->string('address', 150)->nullable();
            $table->decimal('annual_tax', 10, 2)->default(0);
            $table->date('registered_date')->useCurrent();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('properties'); }
};
