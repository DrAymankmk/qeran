<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->string('whatsapp_message_id')->nullable()->after('reference_id');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
            $table->timestamp('read_at')->nullable()->after('delivered_at');

            $table->index('whatsapp_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->dropIndex(['whatsapp_message_id']);
            $table->dropColumn(['whatsapp_message_id', 'delivered_at', 'read_at']);
        });
    }
};
