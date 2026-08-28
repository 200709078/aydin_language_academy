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
        Schema::table('messages', function (Blueprint $table): void {
            // The public form accepts up to 2,000 characters, so varchar(255)
            // could not safely hold the submitted value.
            $table->text('message')->change();
            $table->string('branch', 20)->nullable()->after('telephone');
            $table->string('status', 20)->default('unread')->after('message');
            $table->timestamp('read_at')->nullable()->after('status');
            $table->unsignedBigInteger('read_by')->nullable()->after('read_at');
            $table->timestamp('last_replied_at')->nullable()->after('read_by');
            $table->unsignedBigInteger('last_replied_by')->nullable()->after('last_replied_at');

            $table->foreign('read_by', 'msg_read_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('last_replied_by', 'msg_last_reply_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->index(['status', 'created_at'], 'msg_status_created_idx');
            $table->index('branch', 'msg_branch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table): void {
            $table->dropForeign('msg_read_by_fk');
            $table->dropForeign('msg_last_reply_by_fk');
            $table->dropIndex('msg_status_created_idx');
            $table->dropIndex('msg_branch_idx');
            $table->dropColumn([
                'branch',
                'status',
                'read_at',
                'read_by',
                'last_replied_at',
                'last_replied_by',
            ]);

            $table->string('message')->change();
        });
    }
};
