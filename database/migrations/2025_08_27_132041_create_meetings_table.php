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
       Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index(); // Add index for faster queries
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->index();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->json('documents')->nullable(); // Use JSON column type
            $table->string('district_selection')->nullable()->index();
            $table->string('agenda_leader')->nullable();
            $table->json('default_agenda_items')->nullable(); // Use JSON column type
            $table->json('custom_agenda_items')->nullable(); // Use JSON column type
            $table->json('closing_remarks')->nullable(); // Use JSON column type
            $table->timestamps();

            // Add composite indexes for common queries
            $table->index(['status', 'created_at']);
            $table->index(['district_selection', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
