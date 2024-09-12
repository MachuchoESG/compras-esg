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
            $table->integer('proyecto_id')->default(0)->after('unidad');
            $table->string('proyecto')->nullable()->default(null)->after('proyecto_id');
            $table->string('fechanoautorizacion')->nullable()->default(null)->after('proyecto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn('proyecto_id');
            $table->dropColumn('proyecto');
        });
    }
};
