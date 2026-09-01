<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert the removed archive state into the private draft state.
     */
    public function up(): void
    {
        DB::table('achievements')
            ->where('status', 'archived')
            ->update(['status' => 'draft']);

        DB::table('achievement_entries')
            ->where('status', 'archived')
            ->update(['status' => 'draft']);
    }

    /**
     * The previous archive state is intentionally not restored.
     */
    public function down(): void
    {
        // No-op: draft is the replacement for the removed archive status.
    }
};
