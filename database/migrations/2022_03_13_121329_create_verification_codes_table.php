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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('objective'); //\App\Helpers\Constant::VERIFICATION_OBJECTIVE
            $table->string('code');
            $table->tinyInteger('information_type'); //\App\Helpers\Constant::VERIFICATION_INFORMATION_TYPE
            $table->string('country_code', 6)->nullable();
            $table->string('information', 100);
            $table->boolean('used')->default(\App\Helpers\Constant::VERIFICATION_USED['Not used']);
            $table->dateTime('expired_at')->nullable();
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
        Schema::dropIfExists('verification_codes');
    }
};
