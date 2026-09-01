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
        Schema::table('campaign_page_settings', function (Blueprint $table): void {
            $table->text('description_tr')->nullable()->after('title_en');
            $table->text('description_en')->nullable()->after('description_tr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_page_settings', function (Blueprint $table): void {
            $table->dropColumn(['description_tr', 'description_en']);
        });
    }
};
