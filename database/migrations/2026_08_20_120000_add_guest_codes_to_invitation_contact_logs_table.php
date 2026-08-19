<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->unsignedSmallInteger('invitation_count')->default(1)->after('phone');
            $table->json('guest_codes')->nullable()->after('invitation_count');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->dropColumn(['invitation_count', 'guest_codes']);
        });
    }
};
