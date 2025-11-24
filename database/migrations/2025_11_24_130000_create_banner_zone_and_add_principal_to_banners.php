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
        Schema::table('banners', function (Blueprint $table) {
            if (! Schema::hasColumn('banners', 'principal')) {
                $table->boolean('principal')->default(false)->after('active');
            }
        });

        Schema::create('banner_zone', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banners')->onDelete('cascade');
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['banner_id','zone_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner_zone');

        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'principal')) {
                $table->dropColumn('principal');
            }
        });
    }
};
