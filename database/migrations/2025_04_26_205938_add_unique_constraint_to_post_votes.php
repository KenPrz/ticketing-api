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
        Schema::table('post_votes', function (Blueprint $table) {
            $table->unique(['user_id', 'post_id'], 'user_post_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_votes', function (Blueprint $table) {
            $table->dropUnique('user_post_unique');
        });
    }
};