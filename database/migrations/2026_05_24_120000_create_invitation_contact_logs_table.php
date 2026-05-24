<?php

use App\Helpers\Constant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_contact_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name');
            $table->string('country_code', 8);
            $table->string('phone', 32);
            $table->unsignedTinyInteger('send_status')
                ->default(Constant::INVITATION_CONTACT_SEND_STATUS['pending']);
            $table->unsignedTinyInteger('seen')
                ->default(Constant::SEEN_STATUS['not in the app']);
            $table->text('error_message')->nullable();
            $table->string('reference_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'invited_by']);
            $table->index(['invitation_id', 'phone', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_contact_logs');
    }
};
