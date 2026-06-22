<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->enum('admin_status', ['pending', 'resolved'])->default('pending')->after('reply');
            $table->longText('admin_reply')->nullable()->after('admin_status');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null')->after('admin_reply');
            $table->timestamp('escalated_at')->nullable()->after('admin_id');
            $table->timestamp('resolved_at')->nullable()->after('escalated_at');
            $table->string('escalation_reason')->nullable()->after('resolved_at');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn([
                'admin_status',
                'admin_reply',
                'admin_id',
                'escalated_at',
                'resolved_at',
                'escalation_reason'
            ]);
        });
    }
};
