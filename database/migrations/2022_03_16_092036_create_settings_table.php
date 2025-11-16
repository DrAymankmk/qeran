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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('key')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('setting_translations', function(Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 2)->index();
            $table->string('title', 150)->nullable();
            $table->text('content');
            $table->unique(['setting_id','locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
