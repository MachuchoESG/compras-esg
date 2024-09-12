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
        Schema::create('permisosautorizacionrequisiciones', function (Blueprint $table) {
            $table->id();
            $table->integer('PuestoSolicitante_id');
            $table->integer('PuestoAutorizador_id');
            $table->integer('Departamento_id');
            $table->float('monto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisosautorizacionrequisiciones');
    }
};
