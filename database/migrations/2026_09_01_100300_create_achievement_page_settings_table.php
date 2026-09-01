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
        Schema::create('achievement_page_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('title_tr');
            $table->string('title_en');
            $table->text('description_tr');
            $table->text('description_en');
            $table->unsignedBigInteger('hero_media_asset_id')->nullable();
            $table->timestamps();

            $table->foreign('hero_media_asset_id', 'achievement_page_hero_asset_fk')
                ->references('id')
                ->on('media_assets')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_page_settings');
    }
};
