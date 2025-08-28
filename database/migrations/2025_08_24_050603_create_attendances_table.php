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
            $table->string('employee_id')->unique();
            $table->date('attendance_date');
            $table->time('check_in_time');
            $table->time('expected_time')->default('08:00:00'); // Default expected time
            $table->enum('status', ['on_time', 'late', 'early'])->default('on_time');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add index for better performance
            $table->index(['attendance_date', 'employee_id']);
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
