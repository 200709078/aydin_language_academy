<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rebuild the demo-only achievements schema and reload its mapped SQL snapshot.
     */
    public function up(): void
    {
        Schema::dropIfExists('achievement_entries');
        Schema::dropIfExists('achievement_years');

        Schema::create('achievements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();

            $table->unique('year', 'ach_year_uq');
            $table->index(['status', 'sort_order', 'year'], 'ach_status_order_year_ix');
        });

        Schema::create('achievement_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('achievements_id');
            $table->string('full_name');
            $table->unsignedInteger('sort_order')->nullable();
            $table->string('university_name')->nullable();
            $table->string('department_name')->nullable();
            $table->text('description')->nullable();
            $table->string('branch', 20)->nullable();
            $table->string('card_sub_title', 100)->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('name_permission_status', 20)->default('unknown');
            $table->timestamps();

            $table->foreign('achievements_id', 'ach_entry_achievement_fk')
                ->references('id')
                ->on('achievements')
                ->restrictOnDelete();
            $table->index(['achievements_id', 'status', 'sort_order'], 'ach_entry_parent_status_ix');
            $table->index('name_permission_status', 'ach_entry_permission_ix');
        });

        if (! app()->environment('production')) {
            $this->loadSqlSnapshot('achievements.sql');
            $this->loadSqlSnapshot('achievement_entries.sql');
        }
    }

    /**
     * Restore the immediately preceding table shape without its demo rows.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_entries');
        Schema::dropIfExists('achievements');

        Schema::create('achievement_years', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by', 'ach_year_creator_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('published_by', 'ach_year_publisher_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unique('year', 'ach_year_year_uq');
            $table->index(['status', 'published_at'], 'ach_year_status_pub_ix');
            $table->index(['sort_order', 'year'], 'ach_year_order_year_ix');
        });

        Schema::create('achievement_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('achievement_year_id');
            $table->string('student_full_name');
            $table->string('name_permission_status', 20)->default('unknown');
            $table->date('name_permission_at')->nullable();
            $table->text('name_permission_note')->nullable();
            $table->string('university_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('achievement_type', 100)->nullable();
            $table->text('result_note')->nullable();
            $table->string('branch', 20)->nullable();
            $table->string('program_label', 100)->nullable();
            $table->text('verification_note')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('display_order')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('achievement_year_id', 'ach_entry_year_fk')
                ->references('id')
                ->on('achievement_years')
                ->restrictOnDelete();
            $table->foreign('verified_by', 'ach_entry_verifier_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('created_by', 'ach_entry_creator_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('published_by', 'ach_entry_publisher_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->index(['achievement_year_id', 'status', 'published_at'], 'ach_entry_year_status_pub_ix');
            $table->index(['achievement_year_id', 'display_order'], 'ach_entry_year_order_ix');
            $table->index('name_permission_status', 'ach_entry_name_permission_ix');
        });
    }

    private function loadSqlSnapshot(string $file): void
    {
        $path = database_path('seeders/data/' . $file);
        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new \RuntimeException("Başarı demo SQL dosyası okunamadı: {$file}");
        }

        DB::unprepared($sql);
    }
};
