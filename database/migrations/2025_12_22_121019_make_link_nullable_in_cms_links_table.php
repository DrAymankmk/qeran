<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to alter the column without requiring doctrine/dbal
        DB::statement('ALTER TABLE `cms_links` MODIFY COLUMN `link` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to NOT NULL (set existing NULL values to empty string first)
        DB::statement('UPDATE `cms_links` SET `link` = "" WHERE `link` IS NULL');
        DB::statement('ALTER TABLE `cms_links` MODIFY COLUMN `link` VARCHAR(255) NOT NULL');
    }
};
