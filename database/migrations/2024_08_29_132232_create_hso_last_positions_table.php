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
        Schema::create('hso_last_positions', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->nullable();
            $table->integer('asset_ID')->nullable();
            $table->string('asset_code',50)->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('latitude', 30)->nullable();
            $table->string('longitude', 30)->nullable();
            $table->longText('address')->nullable();
            $table->string('info', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hso_last_positions');
    }
};
