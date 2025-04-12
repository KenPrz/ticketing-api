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
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('city');
            $table->boolean('is_cancelled')->default(false)->after('is_published');
            $table->string('cancelled_reason')->nullable()->after('is_cancelled');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_reason');
            $table->dateTime('published_at')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_published');
            $table->dropColumn('is_cancelled');
            $table->dropColumn('cancelled_reason');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('published_at');
        });
    }
};
