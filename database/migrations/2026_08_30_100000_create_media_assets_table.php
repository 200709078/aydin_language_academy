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
        Schema::create('media_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('disk', 32);
            $table->string('path', 2048);
            $table->char('path_hash', 64);
            $table->string('kind', 20);
            $table->string('visibility', 20)->default('private');
            $table->string('original_filename');
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('checksum', 128)->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by', 'media_asset_uploader_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unique(['disk', 'path_hash'], 'media_asset_disk_path_uq');
            $table->index(['visibility', 'kind', 'created_at'], 'media_asset_visibility_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
