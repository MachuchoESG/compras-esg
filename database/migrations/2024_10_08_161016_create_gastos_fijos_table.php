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
        Schema::create('gastos_fijos', function (Blueprint $table) {
            $table->id();  // id autoincremental para la tabla
            $table->unsignedBigInteger('id_empresa'); // ID de la empresa
            $table->unsignedBigInteger('id_sucursal'); // ID de la sucursal
            $table->unsignedBigInteger('id_producto'); // ID del producto
            $table->string('producto_name');  // Nombre del producto
            $table->timestamps();  // Marcas de tiempo para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos_fijos');
    }
};
