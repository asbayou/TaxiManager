<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('taxis', function (Blueprint $table) {
        $table->id();
        $table->string('plate_number')->unique();
        $table->string('model')->nullable();
        $table->string('year')->nullable();
        $table->enum('calculation_type', ['fixed', 'per_km']);
        $table->decimal('fixed_daily_price', 8, 2)->nullable();
        $table->decimal('price_per_km', 8, 2)->nullable();
        $table->text('notes')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxis');
    }
};
