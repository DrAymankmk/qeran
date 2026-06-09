<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('acceptance_status')->nullable()->after('seen');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_contact_logs', function (Blueprint $table) {
            $table->dropColumn('acceptance_status');
        });
    }
};
