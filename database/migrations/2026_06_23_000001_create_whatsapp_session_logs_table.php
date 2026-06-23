<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_session_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('event', 64)->index();
            $table->string('level', 16)->default('info');
            $table->string('message');
            $table->json('context')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_session_logs');
    }
};
