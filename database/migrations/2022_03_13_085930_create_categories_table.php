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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(\App\Helpers\Constant::CATEGORY_STATUS['Active']);
            $table->foreignId('parent_id')->nullable()->references('id')
                ->on('categories')->nullOnDelete();
            $table->boolean('is_wedding')->default(\App\Helpers\Constant::CATEGORY_STATUS['Not active']);
            $table->boolean('is_party')->default(\App\Helpers\Constant::CATEGORY_STATUS['Not active']);
            $table->timestamps();
        });

        Schema::create('category_translations', function(Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 2)->index();
            $table->string('name');
            $table->string('slug');
            $table->unique(['category_id','locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
