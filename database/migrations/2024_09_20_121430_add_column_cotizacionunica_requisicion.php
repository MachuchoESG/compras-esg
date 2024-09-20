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
        Schema::table('requisiciones', function (Blueprint $table) {
            // Agregar la columna cotizacion_unica de tipo boolean con valor por defecto 0
            $table->boolean('cotizacion_unica')->default(0)->after('visto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            // Eliminar la columna cotizacion_unica si se revierte la migración
            $table->dropColumn('cotizacion_unica');
        });
    }
};
