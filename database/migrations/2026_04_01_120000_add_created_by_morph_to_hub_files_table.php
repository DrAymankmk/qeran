<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * For databases that already ran the original hub_files migration without created_by.
     */
    public function up(): void
    {
        if (!Schema::hasTable('hub_files')) {
            return;
        }

        if (Schema::hasColumn('hub_files', 'created_by_id')) {
            return;
        }

        Schema::table('hub_files', function (Blueprint $table) {
            $table->nullableMorphs('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('hub_files')) {
            return;
        }

        if (!Schema::hasColumn('hub_files', 'created_by_id')) {
            return;
        }

        Schema::table('hub_files', function (Blueprint $table) {
            $table->dropMorphs('created_by');
        });
    }
};
