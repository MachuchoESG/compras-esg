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
        Schema::create('tokens', function (Blueprint $table) {
            $table->id(); // id de la tabla
            $table->unsignedBigInteger('user_id'); // columna user_id
            $table->string('token'); // columna token (cadena de texto)
            $table->boolean('activo')->default(true); // columna activo (booleano)
            $table->timestamps(); // created_at y updated_at

            // Relación con la tabla de usuarios (asume que tienes una tabla 'users')
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_tokens');
    }
};
