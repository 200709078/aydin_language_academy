<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achievements', function (Blueprint $table): void {
            $table->dropUnique('ach_year_uq');
            $table->dropIndex('ach_status_order_year_ix');
            $table->dropColumn('year');
        });

        Schema::table('achievements', function (Blueprint $table): void {
            $table->index(['status', 'sort_order'], 'ach_status_order_ix');
        });
    }

    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table): void {
            $table->dropIndex('ach_status_order_ix');
        });

        Schema::table('achievements', function (Blueprint $table): void {
            $table->unsignedSmallInteger('year')->after('id');
            $table->unique('year', 'ach_year_uq');
            $table->index(['status', 'sort_order', 'year'], 'ach_status_order_year_ix');
        });
    }
};
