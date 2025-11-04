<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('banner_clicks', function (Blueprint $table) {
            if (!Schema::hasColumn('banner_clicks', 'assignment_id')) {
                $table->foreignId('assignment_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('banner_clicks', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('banner_clicks', 'site_domain')) {
                $table->string('site_domain')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('banner_clicks', function (Blueprint $table) {
            $table->dropConstrainedForeignIdIfExists('assignment_id');
            $table->dropConstrainedForeignIdIfExists('zone_id');
            $table->dropColumn('site_domain');
        });
    }
};
