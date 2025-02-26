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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('events');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('purchase_id')
                ->constrained('purchases')
                ->unique(); // Each purchase should be for a unique seat
            $table->boolean('is_occupied')
                ->default(false);
            $table->unique(['event_id', 'purchase_id']); // A purchase can only reserve one seat per event
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};

// event_id
// user_id
// purchase_id
// is_occupied