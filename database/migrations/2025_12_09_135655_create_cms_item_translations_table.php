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
        Schema::create('cms_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_item_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('title'); // REQUIRED
            $table->string('sub_title')->nullable();
            $table->text('content')->nullable();
            $table->string('icon')->nullable(); // Icon class or path
            $table->timestamps();

            $table->unique(['cms_item_id', 'locale']);

            // Add index for locale for better query performance
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_item_translations');
    }
};
