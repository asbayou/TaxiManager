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
    Schema::create('daily_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('taxi_id')->constrained('taxis')->onDelete('cascade');
        $table->date('date');
        $table->decimal('kilometers', 8, 2)->default(0);
        $table->decimal('earnings', 8, 2)->default(0);
        $table->decimal('expenses', 8, 2)->default(0);
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_records');
    }
};
