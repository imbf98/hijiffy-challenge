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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->index();
            $table->foreignId('property_id')->constrained('properties');
            $table->integer('max_guests')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->index('max_guests', 'rooms_max_guests_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
