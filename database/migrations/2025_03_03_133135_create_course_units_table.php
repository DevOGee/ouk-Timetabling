<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // E.g., MAT101
            $table->string('name'); // E.g., Calculus I
            $table->string('color')->nullable(); // Add color field
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_units');
    }
};
