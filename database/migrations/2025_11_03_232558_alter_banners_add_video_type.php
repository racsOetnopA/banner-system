<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        // Cambiar ENUM para incluir 'video'
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('image','html','video') NOT NULL DEFAULT 'image'");
    }

    public function down(): void
    {
        // Revertir si es necesario
        DB::statement("ALTER TABLE banners MODIFY COLUMN type ENUM('image','html') NOT NULL DEFAULT 'image'");
    }
};
