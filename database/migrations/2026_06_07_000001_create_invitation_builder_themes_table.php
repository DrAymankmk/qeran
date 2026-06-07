<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_builder_themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name_ar', 120);
            $table->string('name_en', 120)->nullable();
            $table->string('category', 32)->default('opening');
            $table->string('media_type', 16);
            $table->string('media_path', 500);
            $table->string('preview_color', 20)->default('#1a1520');
            $table->string('primary_color', 20)->default('#c9a962');
            $table->string('secondary_color', 20)->default('#e8b4b8');
            $table->string('background_color', 20)->default('#1a1520');
            $table->string('text_color', 20)->default('#faf6f0');
            $table->string('renderer', 64)->default('builder-wedding');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_builder_themes');
    }
};
