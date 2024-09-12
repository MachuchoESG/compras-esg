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
        // Modificar la clave foránea en la tabla 'detalle_cotizaciones'
        Schema::table('detalle_cotizaciones', function (Blueprint $table) {
            // Primero, elimina la clave foránea existente si es necesario
            $table->dropForeign(['cotizacion_id']);

            // Luego, agrega la clave foránea con la opción de borrado en cascada
            $table->foreign('cotizacion_id')
                ->references('id')
                ->on('cotizaciones')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_cotizaciones', function (Blueprint $table) {
            // Primero, elimina la clave foránea existente con borrado en cascada
            $table->dropForeign(['cotizacion_id']);

            // Luego, agrega la clave foránea sin la opción de borrado en cascada
            $table->foreign('cotizacion_id')
                ->references('id')
                ->on('cotizaciones');
        });
    }
};
