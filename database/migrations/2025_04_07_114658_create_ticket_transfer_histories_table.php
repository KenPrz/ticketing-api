<?php

use App\Enums\TicketTransferStatus;
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
        Schema::create('ticket_transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->onDelete('cascade');
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('to_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamp('transfer_date')
                ->nullable();
                $table->enum('status',
                array_map(fn($type) => $type->value, TicketTransferStatus::cases())
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_transfer_histories');
    }
};
