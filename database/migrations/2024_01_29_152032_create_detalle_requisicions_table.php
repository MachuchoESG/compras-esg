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
        Schema::create('detalle_requisiciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('requisicion_id');
            $table->foreign('requisicion_id')->references('id')->on('requisiciones');


            $table->unsignedBigInteger('producto_id')->nullable();

            $table->float('cantidad');

            $table->string('producto');

            $table->string('observaciones');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_requisicions');
    }
};
