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
        Schema::create('hso_geofences', function (Blueprint $table) {
            $table->id();
            $table->integer('idpoi')->nullable();
            $table->string('transporter', 50)->nullable();
            $table->string('name')->nullable();
            $table->timestamp('TrackingDate')->nullable();
            $table->string('FenceCode')->nullable();
            $table->string('Acc',10)->nullable();
            $table->timestamp('EnterDateTimeArea')->nullable();
            $table->timestamp('OutDateTimeArea')->nullable();
            $table->string('status', 3)->nullable();
            $table->string('info', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hso_geofences');
    }
};
