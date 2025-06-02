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
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms');
            $table->date('date')->index();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->unique(['room_id', 'date'], 'room_availability_unique');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['room_id', 'date', 'is_available'], 'room_availability_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};
