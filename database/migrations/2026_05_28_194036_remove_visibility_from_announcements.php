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
        // Remove visibility table entirely
        Schema::dropIfExists('announcement_visibility');

        // If announcements table has visibility-related columns, drop them
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'visibility')) {
                $table->dropColumn('visibility');
            }
            if (Schema::hasColumn('announcements', 'visible_to')) {
                $table->dropColumn('visible_to');
            }
        });
    }

    public function down(): void
    {
        // No easy way to rollback a destructive removal of structure
    }
};
