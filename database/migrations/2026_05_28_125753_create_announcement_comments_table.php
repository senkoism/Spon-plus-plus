<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_comments', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->unsignedBigInteger('parent_id')->nullable();
            $blueprint->text('body');
            $blueprint->timestamps();

            $blueprint->foreign('parent_id')->references('id')->on('announcement_comments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_comments');
    }
};
