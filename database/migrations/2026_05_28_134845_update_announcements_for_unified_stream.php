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
            if (!Schema::hasColumn('announcements', 'type')) {
                $table->enum('type', ['announcement', 'material', 'assignment'])->default('announcement')->after('user_id');
            }
            if (!Schema::hasColumn('announcements', 'file_path')) {
                $table->string('file_path')->nullable()->after('content');
            }
            if (!Schema::hasColumn('announcements', 'open_date')) {
                $table->timestamp('open_date')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('announcements', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('open_date');
            }
            if (!Schema::hasColumn('announcements', 'order')) {
                $table->integer('order')->default(0)->after('due_date');
            }
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_submissions', 'announcement_id')) {
                $table->unsignedBigInteger('announcement_id')->nullable()->after('material_id');
            }
        });

        Schema::table('folder_files', function (Blueprint $table) {
            if (!Schema::hasColumn('folder_files', 'announcement_id')) {
                $table->unsignedBigInteger('announcement_id')->nullable()->after('material_id');
            }
        });

        // Migrate materials to announcements stream
        if (Schema::hasTable('materials')) {
            $materials = DB::table('materials')->get();
            foreach ($materials as $m) {
                // Check if already migrated to avoid duplicates
                $exists = DB::table('announcements')
                    ->where('classroom_id', $m->classroom_id)
                    ->where('title', $m->title)
                    ->where('created_at', $m->created_at)
                    ->exists();
                
                if (!$exists) {
                    $type = ($m->type === 'assignment') ? 'assignment' : 'material';
                    
                    $announcementId = DB::table('announcements')->insertGetId([
                        'classroom_id' => $m->classroom_id,
                        'user_id'      => $m->user_id,
                        'type'         => $type,
                        'title'        => $m->title,
                        'content'      => $m->content ?? '',
                        'file_path'    => $m->file_path ?? null,
                        'open_date'    => $m->open_date ?? null,
                        'due_date'     => $m->due_date ?? null,
                        'created_at'   => $m->created_at,
                        'updated_at'   => $m->updated_at,
                    ]);

                    DB::table('assignment_submissions')->where('material_id', $m->id)->update(['announcement_id' => $announcementId]);
                    DB::table('folder_files')->where('material_id', $m->id)->update(['announcement_id' => $announcementId]);
                }
            }
        }

        Schema::disableForeignKeyConstraints();

        // Clean up assignment_submissions
        Schema::table('assignment_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_submissions', 'material_id')) {
                $table->dropForeign(['material_id']);
                $table->dropColumn('material_id');
            }
            if (!Schema::hasColumn('assignment_submissions', 'announcement_id')) {
                 $table->foreignId('announcement_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // Clean up folder_files
        Schema::table('folder_files', function (Blueprint $table) {
            if (Schema::hasColumn('folder_files', 'material_id')) {
                $table->dropForeign(['material_id']);
                $table->dropColumn('material_id');
            }
            if (!Schema::hasColumn('folder_files', 'announcement_id')) {
                $table->foreignId('announcement_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        Schema::dropIfExists('material_completions');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('sections');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Reversal is complex, usually not needed for these destructive refactors in local dev
    }
};
