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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('photo_path')->nullable(); // Add photo_path column
            $table->boolean('status')->default(false)->change(); // Update status default value to false
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('photo_path'); // Drop photo_path column
            $table->boolean('status')->default(true)->change(); // Revert status default value to true
        });
    }
};
