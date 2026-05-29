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
            $table->renameColumn('due_date', 'open_date');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->timestamp('due_date')->nullable()->after('open_date');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('open_date', 'due_date');
        });
    }
};
