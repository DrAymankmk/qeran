<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_links', function (Blueprint $table) {
            $table->id();
            $table->morphs('linkable'); // Creates linkable_id and linkable_type columns
            // linkable_type can be: App\Models\CmsPage, App\Models\CmsSection, or App\Models\CmsItem
            $table->string('name'); // Link name/label
            $table->string('link'); // URL or link
            $table->string('icon')->nullable(); // Icon class (FontAwesome, Material Icons, etc.) or icon path
            $table->string('target')->default('_self'); // _self, _blank, etc.
            $table->string('type')->nullable(); // Optional: 'social', 'contact', 'quick', 'custom', etc.
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['linkable_type', 'linkable_id']);
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_links');
    }
};
