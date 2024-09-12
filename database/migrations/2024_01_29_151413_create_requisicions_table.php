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
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id();

            $table->string('folio');
            $table->string('observaciones');
            $table->string('proveedor')->nullable();
            $table->integer('estatus_id')->default(1);
            $table->string('ordenCompra')->nullable()->default(null);
            $table->unsignedBigInteger('empleado_id')->nullable();



            $table->unsignedBigInteger('sucursal_id');
            $table->foreign('sucursal_id')->references('id')->on('sucursales');


            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('proveedor_id')->nullable();


            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->timestamp('fechaaprobacion')->nullable();
            $table->timestamp('fechacancelacion')->nullable();
            $table->boolean('seguimiento')->default(false);
            $table->boolean('visto')->default(false);
            $table->boolean('aprobado')->default(false);
            $table->string('unidad')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicions');
    }
};
