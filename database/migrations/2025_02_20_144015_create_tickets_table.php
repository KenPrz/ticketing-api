<?php

use App\Enums\TicketType;
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
            $table->text('qr_code');
            $table->string('ticket_name');
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');
            $table->foreignId('owner_id')
                ->constrained('users');
            $table->foreignId('ticket_tier_id')
                ->constrained('event_ticket_tiers')
                ->onDelete('cascade');
            $table->foreignId('purchase_id')
                ->nullable()
                ->constrained('purchases')
                ->nullOnDelete();
            $table->string('ticket_type');
            $table->text('ticket_desc')->nullable();
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