<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVidangesTable extends Migration
{
    public function up()
    {
        Schema::create('vidanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxi_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('kilometers'); // KM at the moment of vidange
            $table->decimal('cost', 10, 2)->nullable(); // total cost of vidange
            $table->text('notes')->nullable(); // what was done
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vidanges');
    }
}
