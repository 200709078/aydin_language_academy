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
        Schema::create('contact_message_replies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->string('recipient_email');
            $table->string('subject', 150);
            $table->text('body');
            $table->string('delivery_status', 20)->default('queued');
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->foreign('message_id', 'msg_reply_message_fk')
                ->references('id')
                ->on('messages')
                ->restrictOnDelete();
            $table->foreign('sent_by', 'msg_reply_sender_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->index(['message_id', 'created_at'], 'msg_reply_message_created_idx');
            $table->index(['delivery_status', 'created_at'], 'msg_reply_status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_message_replies');
    }
};
