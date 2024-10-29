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
            $table->boolean('cotizacion_especial')->default(false);//string('proyecto')->nullable()->default(null)->after('proyecto_id');
            $table->integer('departamento_especial')->nullable()->default(null);
            $table->string('observacion_especial')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn('cotizacion_especial');
            $table->dropColumn('departamento_especial');
            $table->dropColumn('observacion_especial');
        });
    }
};
