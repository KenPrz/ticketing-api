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
            $table->foreignId('ticket_id')
                ->unique()
                ->constrained('tickets')
                ->onDelete('cascade');
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');
            $table->string('row')->nullable();
            $table->string('number')->nullable();
            $table->string('section')->nullable();
            $table->boolean('is_occupied')
                ->default(false);
            $table->timestamps();

            $table->unique(['event_id', 'row', 'number'], 'unique_seat_location');
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