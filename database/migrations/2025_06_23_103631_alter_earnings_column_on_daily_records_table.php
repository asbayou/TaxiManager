<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('daily_records', function (Blueprint $table) {
        $table->decimal('earnings', 12, 2)->change(); // allows up to 9999999999.99
    });
}

public function down()
{
    Schema::table('daily_records', function (Blueprint $table) {
        $table->decimal('earnings', 8, 2)->change(); // rollback to original
    });
}

};
