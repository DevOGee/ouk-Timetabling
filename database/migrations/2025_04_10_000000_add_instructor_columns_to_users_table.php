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
        Schema::table('users', function (Blueprint $table) {
            // Add user profile columns
            $table->foreignId('title_id')->nullable()->after('id')->constrained('titles')->onDelete('set null');
            $table->string('image_path')->nullable()->after('email_verified_at');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('image_path');
            $table->string('phone')->nullable()->after('status');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('users', 'title_id')) {
                $table->dropForeign(['title_id']);
                $table->dropColumn('title_id');
            }
            
            // Drop other columns
            $table->dropColumn([
                'image_path',
                'status',
                'phone',
                'last_login_at'
            ]);
        });
    }
};
