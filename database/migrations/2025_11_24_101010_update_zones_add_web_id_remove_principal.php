<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // Añadir web_id nullable y clave foránea a webs
            $table->unsignedBigInteger('web_id')->nullable()->after('id');
            $table->foreign('web_id')->references('id')->on('webs')->onDelete('set null');

            // Eliminar columna principal si existe
            if (Schema::hasColumn('zones', 'principal')) {
                $table->dropColumn('principal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // Restaurar columna principal
            $table->boolean('principal')->default(false)->after('height');

            // Eliminar foreign key y columna web_id
            if (Schema::hasColumn('zones', 'web_id')) {
                $table->dropForeign(['web_id']);
                $table->dropColumn('web_id');
            }
        });
    }
};
