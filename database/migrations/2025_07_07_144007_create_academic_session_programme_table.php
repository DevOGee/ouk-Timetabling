<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_session_programme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('programme_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['academic_session_id', 'programme_id'], 'acad_sess_prog_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_session_programme');
    }
};
