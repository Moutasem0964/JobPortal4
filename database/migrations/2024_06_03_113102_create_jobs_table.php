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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('job_title');
            $table->string('employment');
            $table->string('gender');
            $table->integer('min_age');
            $table->integer('max_age');
            $table->string('educational_level');
            $table->string('career_level');
            $table->json('languages');
            $table->integer('number_of_vacancies');
            $table->json('type_of_employment');
            $table->string('city');
            $table->string('address');
            $table->string('min_salary');
            $table->string('max_salary');
            $table->text('job_description');
            $table->boolean('cover_letter_required');
            $table->boolean('status')->default(false);
            $table->boolean('disabled_by_admin')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
