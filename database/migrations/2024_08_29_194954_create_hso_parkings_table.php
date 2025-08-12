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
        Schema::create('hso_parkings', function (Blueprint $table) {
            $table->id();
            $table->integer('eventID')->nullable();
            $table->integer('accountID')->nullable();
            $table->integer('assetID')->nullable();
            $table->string('asset_code', 30)->nullable();
            $table->string('transporter', 50)->nullable();
            $table->integer('acc')->nullable();
            $table->string('latitude', 30)->nullable();
            $table->string('longitude', 30)->nullable();
            $table->timestamp('off')->nullable();
            $table->timestamp('on')->nullable();
            $table->string('duration')->nullable();
            $table->text('address')->nullable();
            $table->decimal('distanceKM', 8, 2)->nullable();
            $table->integer('status')->nullable();
            $table->string('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hso_parkings');
    }
};
