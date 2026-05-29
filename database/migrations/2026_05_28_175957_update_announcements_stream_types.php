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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('type')->change(); // Temporary change to allow updating the column easily if it's enum
            $table->string('original_filename')->nullable()->after('file_path');
        });
        
        // Use raw query for enum update to be reliable across DB drivers
        DB::statement("ALTER TABLE announcements MODIFY COLUMN type ENUM('announcement', 'material', 'assignment', 'document', 'pdf', 'spreadsheet', 'presentation', 'image', 'archive', 'link', 'txt', 'drawio') DEFAULT 'announcement'");
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('original_filename');
        });
    }
};
