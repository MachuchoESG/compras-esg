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
            $table->boolean('borrado')->default(false);    // Columna borrado (booleano)
            $table->date('fecha_borrado')->nullable();     // Columna fecha_borrado (date)
            $table->unsignedBigInteger('user_borrado')->nullable();

            $table->foreign('user_borrado')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn(['borrado', 'fecha_borrado', 'user_borrado']);
        });
    }
};
