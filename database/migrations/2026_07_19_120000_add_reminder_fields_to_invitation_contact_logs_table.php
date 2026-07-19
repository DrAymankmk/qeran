<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('read_at');
            $table->text('reminder_error_message')->nullable()->after('reminder_sent_at');

            $table->index('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->dropIndex(['reminder_sent_at']);
            $table->dropColumn(['reminder_sent_at', 'reminder_error_message']);
        });
    }
};
