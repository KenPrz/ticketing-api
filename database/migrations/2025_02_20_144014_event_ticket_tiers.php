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
        Schema::create('event_ticket_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('tier_name');
            $table->enum('ticket_type',
                array_map(fn($type) => $type->value, TicketType::cases())
            );
            $table->text('ticket_desc')->nullable();
            $table->float('price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_ticket_tiers');
    }
};