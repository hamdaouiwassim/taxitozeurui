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
        Schema::create('taxis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('plate_number')->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color')->default('White');
            $table->string('capacity')->default('4 Passengers');
            $table->string('luggage')->default('3 Bags');
            $table->string('type')->default('economy');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxis');
    }
};
