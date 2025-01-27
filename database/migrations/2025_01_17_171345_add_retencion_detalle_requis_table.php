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
        Schema::table('detalle_requisiciones', function (Blueprint $table) {
            $table->float('retencion')->default(0)->after('producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_requisiciones', function (Blueprint $table) {
            $table->dropColumn('retencion');
        });
    }
};
