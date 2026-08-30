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
        Schema::create('news', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug', 191);
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('cover_media_asset_id')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('unpublished_at')->nullable();
            $table->string('display_location', 20)->default('none');
            $table->unsignedInteger('sort_order')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 320)->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cover_media_asset_id', 'news_cover_asset_fk')
                ->references('id')
                ->on('media_assets')
                ->restrictOnDelete();
            $table->foreign('author_id', 'news_author_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('published_by', 'news_publisher_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unique('slug', 'news_slug_uq');
            $table->index(['status', 'published_at'], 'news_status_pub_ix');
            $table->index(['display_location', 'sort_order', 'published_at'], 'news_display_pub_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
