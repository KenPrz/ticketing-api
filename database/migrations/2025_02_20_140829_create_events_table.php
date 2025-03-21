<?php

use App\Enums\EventCategory;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('organizer_id')
                ->constrained('users');
            $table->enum('category',
                array_map(fn($type) => $type->value, EventCategory::cases())
            );
            $table->date('date');
            $table->string('time');
            $table->text('description');
            $table->string('venue');
            $table->string('city');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};