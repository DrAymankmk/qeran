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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('package_invitation_type')->default(\App\Helpers\Constant::INVITATION_TYPE['App Design']);
            $table->tinyInteger('package_type')->default(\App\Helpers\Constant::PACKAGE_TYPE['Static Package']);
            $table->boolean('active')->default(\App\Helpers\Constant::PACKAGE_STATUS['Active']);
            $table->integer('count')->default(0);
            $table->integer('free_invitations_count')->default(0);
            $table->float('price', 13, 2)->default(0);
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
        Schema::dropIfExists('packages');
    }
};
