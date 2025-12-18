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
        Schema::create('cms_link_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_link_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name'); // Translated link name
            $table->timestamps();

            $table->unique(['cms_link_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_link_translations');
    }
};
