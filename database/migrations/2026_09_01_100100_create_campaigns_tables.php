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
        Schema::create('campaign_page_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('title_tr');
            $table->string('title_en');
            $table->unsignedBigInteger('hero_media_asset_id')->nullable();
            $table->timestamps();

            $table->foreign('hero_media_asset_id', 'campaign_page_hero_asset_fk')
                ->references('id')
                ->on('media_assets')
                ->restrictOnDelete();
        });

        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('title_tr');
            $table->string('title_en');
            $table->text('description_tr');
            $table->text('description_en');
            $table->string('link_type', 20)->default('none');
            $table->string('internal_destination', 100)->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['status', 'sort_order'], 'campaign_status_sort_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('campaign_page_settings');
    }
};
