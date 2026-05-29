<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Rename content to description if it exists
            if (Schema::hasColumn('announcements', 'content') && !Schema::hasColumn('announcements', 'description')) {
                $table->renameColumn('content', 'description');
            }

            // Ensure type is plain string for now to avoid enum constraints during transition
            $table->string('type')->change();

            // Add file_type if it doesn't exist
            if (!Schema::hasColumn('announcements', 'file_type')) {
                $table->string('file_type')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'description')) {
                $table->renameColumn('description', 'content');
            }
            $table->dropColumn('file_type');
        });
    }
};
