<?php

use App\Enums\PostContext;
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('event_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('ticket_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->text('content');
            $table->enum('post_context',
                array_map(fn($type) => $type->value, PostContext::cases())
            );
            $table->string('price')
                ->nullable()
                ->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
