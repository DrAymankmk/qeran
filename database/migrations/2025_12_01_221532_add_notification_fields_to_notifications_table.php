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
        Schema::table('notifications', function (Blueprint $table) {
            $table->tinyInteger('category')->nullable()->after('type')->comment('1=>Order, 2=>Payment, 3=>User, 4=>Contact Us');
            $table->tinyInteger('notification_type')->nullable()->after('category')->comment('Specific type within category');
            $table->timestamp('read_at')->nullable()->after('target_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['category', 'notification_type', 'read_at']);
        });
    }
};
