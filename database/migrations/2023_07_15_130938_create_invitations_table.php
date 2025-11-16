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
        /*
         host
name
date
time
address
latitude
longitude
category_id
groom
bride
groom_father
bride_father
event_name
description
invitation_media_type
status
         */
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('invitation_type')->default(\App\Helpers\Constant::INVITATION_TYPE['App Design']);
            $table->tinyInteger('invitation_step')->default(\App\Helpers\Constant::INVITATION_STEP['Upload Invitation']);
            $table->foreignId('category_id')->nullable()->references('id')
                ->on('categories')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->references('id')
                ->on('users')->nullOnDelete();
            $table->tinyInteger('paid')->default(\App\Helpers\Constant::PAID_STATUS['Not Paid']);
            $table->tinyInteger('status')->default(\App\Helpers\Constant::INVITATION_STATUS['Not Approved']);
            $table->string('host_name',50)->nullable();
            $table->string('name',50)->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('address')->nullable();
            $table->string('groom',50)->nullable();
            $table->string('bride',50)->nullable();
            $table->string('groom_father',50)->nullable();
            $table->string('bride_father',50)->nullable();
            $table->string('event_name',50)->nullable();
            $table->integer('count')->nullable();
            $table->integer('price')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('package_id')->nullable()->references('id')
                ->on('packages')->nullOnDelete();

            $table->tinyInteger('invitation_media_type')->default(\App\Helpers\Constant::FILE_TYPE['Image']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invitations');
    }
};
