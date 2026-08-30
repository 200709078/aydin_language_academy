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
        Schema::create('news_content_blocks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('news_id');
            $table->unsignedInteger('position');
            $table->string('type', 24);
            $table->string('content_format', 20)->default('plain');
            $table->string('heading')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedBigInteger('media_asset_id')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('link_label')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('news_id', 'news_block_news_fk')
                ->references('id')
                ->on('news')
                ->restrictOnDelete();
            $table->foreign('media_asset_id', 'news_block_asset_fk')
                ->references('id')
                ->on('media_assets')
                ->restrictOnDelete();
            $table->unique(['news_id', 'position'], 'news_block_position_uq');
            $table->index(['news_id', 'is_active', 'position'], 'news_block_order_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_content_blocks');
    }
};
