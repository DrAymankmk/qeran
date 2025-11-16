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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('account_type')->comment('1=>user,2=>admin')->default(\App\Helpers\Constant::USER_TYPE['User']);
            $table->string('device_id')->nullable();

            $table->boolean('verified')->default(\App\Helpers\Constant::USER_STATUS['Not verified']);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code', 6)->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('address')->nullable();
            $table->float('average_rate', 8 , 1)->default(0);
            $table->integer('rate_count')->default(0);
            $table->integer('notification_count')->default(0);
            $table->tinyInteger('platform')->nullable(); //\App\Helpers\Constant::USER_PLATFORM
            $table->text('description')->nullable();
            $table->tinyInteger('gender')->comment('1=>male,2=>female')->default(\App\Helpers\Constant::USER_GENDER['Male']);
            $table->tinyInteger('register_type')->comment('1=>by app,2=>added by user')->default(\App\Helpers\Constant::REGISTER_TYPE['By App']);
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
