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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('employee_id')->index();
            $table->string('department')->nullable();
            $table->date('attendance_date')->index();
            $table->time('check_in_time');
            $table->time('expected_time')->default('08:00:00');
            $table->enum('status', ['on_time', 'late', 'early'])->default('on_time');
            $table->string('selfie_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Prevent duplicate attendance for same employee on same day
            $table->unique(['employee_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};