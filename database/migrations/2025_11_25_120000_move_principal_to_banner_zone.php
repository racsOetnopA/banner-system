<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add principal column to pivot
        Schema::table('banner_zone', function (Blueprint $table) {
            if (!Schema::hasColumn('banner_zone', 'principal')) {
                $table->boolean('principal')->default(false)->after('zone_id');
            }
        });

        // Migrate existing data: if banners.principal = true then set banner_zone.principal = true
        DB::statement('UPDATE banner_zone bz JOIN banners b ON b.id = bz.banner_id SET bz.principal = b.principal');

        // Remove principal column from banners (if exists)
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'principal')) {
                $table->dropColumn('principal');
            }
        });
    }

    public function down(): void
    {
        // Restore principal column on banners
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'principal')) {
                $table->boolean('principal')->default(false)->after('active');
            }
        });

        // Migrate back: if any pivot principal true for a banner, set banners.principal = true
        DB::statement('UPDATE banners b SET b.principal = (
            SELECT CASE WHEN EXISTS(SELECT 1 FROM banner_zone bz WHERE bz.banner_id = b.id AND bz.principal = 1) THEN 1 ELSE 0 END
        )');

        // Drop pivot column
        Schema::table('banner_zone', function (Blueprint $table) {
            if (Schema::hasColumn('banner_zone', 'principal')) {
                $table->dropColumn('principal');
            }
        });
    }
};
