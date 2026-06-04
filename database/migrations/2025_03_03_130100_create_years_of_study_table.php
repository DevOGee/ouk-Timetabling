<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('years_of_study', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // E.g., First Year, Second Year
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('years_of_study');
    }
};
