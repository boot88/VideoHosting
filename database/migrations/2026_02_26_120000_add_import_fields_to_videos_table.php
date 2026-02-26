<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('source_path')->nullable()->unique()->after('category');
            $table->unsignedBigInteger('source_mtime')->nullable()->after('source_path');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropUnique(['source_path']);
            $table->dropColumn(['source_path', 'source_mtime']);
        });
    }
};
