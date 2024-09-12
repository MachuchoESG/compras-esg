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
        Schema::create('autorizacionhistorial', function (Blueprint $table) {
            $table->id();
            $table->integer('requisicion_id');
            $table->integer('user_id');
            $table->integer('user_solicita');
            $table->integer('departamento_id');
            $table->boolean('visto')->default(0);
            $table->boolean('autorizado')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autorizacionhistorial');
    }
};
