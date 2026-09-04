<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_content_blocks', function (Blueprint $table): void {
            $table->dropColumn('content_format');
        });
    }

    public function down(): void
    {
        Schema::table('news_content_blocks', function (Blueprint $table): void {
            $table->string('content_format', 20)->default('plain')->after('type');
        });
    }
};
