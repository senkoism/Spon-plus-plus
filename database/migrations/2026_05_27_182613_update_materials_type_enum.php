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
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('type', ['document', 'pdf', 'spreadsheet', 'presentation', 'image', 'archive', 'link', 'assignment', 'txt', 'drawio'])->change();
            $table->dateTime('due_date')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->enum('type', ['document', 'pdf', 'spreadsheet', 'presentation', 'link', 'assignment'])->change();
            $table->dropColumn('due_date');
        });
    }
};
