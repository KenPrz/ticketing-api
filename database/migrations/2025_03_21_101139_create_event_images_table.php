<?php

use App\Enums\EventImageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable');
            $table->string('image_url')->nullable();
            $table->enum('image_type',
                array_map(fn($type) => $type->value, EventImageType::cases())
            );
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_images');
    }
};