<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // Intentar eliminar FK previa si existe (silencioso)
            try {
                $table->dropForeign(['web_id']);
            } catch (\Throwable $e) {
                // ignorar si no existe
            }

            // Asegurarnos de tener la columna como unsignedBigInteger not null
            // IMPORTANTE: la operación change() requiere doctrine/dbal
            if (Schema::hasColumn('zones', 'web_id')) {
                $table->unsignedBigInteger('web_id')->nullable(false)->change();
            } else {
                $table->unsignedBigInteger('web_id')->after('id');
            }

            // Añadir FK (restrict para evitar borrar webs con zonas)
            $table->foreign('web_id')->references('id')->on('webs')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // Eliminar FK y volver a nullable
            $table->dropForeign(['web_id']);
            $table->unsignedBigInteger('web_id')->nullable()->change();
        });
    }
};
