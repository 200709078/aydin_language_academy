<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            $table->dropColumn(['seo_title', 'seo_description', 'canonical_url']);
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            $table->string('seo_title')->nullable()->after('published_by');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
            $table->string('canonical_url', 2048)->nullable()->after('seo_description');
        });
    }
};
