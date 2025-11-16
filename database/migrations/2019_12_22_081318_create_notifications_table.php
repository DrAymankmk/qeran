<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type')->comment('0=>admin,1=>invitation')->nullable();

            $table->string('user_id')->nullable();
            $table->integer('target_id')->nullable();
            $table->timestamps();

        });
        Schema::create('notification_translations', function (Blueprint $table) {
            $table->increments('notifications_trans_id');
            $table->bigInteger('notification_id')->unsigned();
            $table->string('locale', 2)->index();
            $table->string('title')->nullable();
            $table->string('description')->nullable();

            $table->unique(['notification_id', 'locale']);
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
