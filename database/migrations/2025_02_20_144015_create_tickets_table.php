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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->binary('qr_code');
            $table->string('ticket_name');
            $table->foreignId('event_id')
                ->constrained('events');
            $table->foreignId('owner_id')
                ->constrained('users');
            $table->text('ticket_desc');
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};