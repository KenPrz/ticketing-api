<?php

use App\Enums\FriendStatus;
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
        Schema::create('user_friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('friend_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('status',
                array_map(fn($type) => $type->value, FriendStatus::cases())
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_friends');
    }
};
// 'user_id',
// 'friend_id',
// 'status',
// 'created_at',
// 'updated_at'