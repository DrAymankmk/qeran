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
        Schema::create('hub_files', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('file_type')->default(\App\Helpers\Constant::FILE_TYPE['Image']);
            $table->tinyInteger('file_key')->default(\App\Helpers\Constant::FILE_KEY['Main']);

            $table->morphs('morphable');
            $table->string('path')->nullable();
            $table->string('bucket_name')->index();
            $table->string('extension')->nullable();
            $table->integer('size')->nullable();
            $table->string('original_name')->nullable();
            $table->string('getMimeType')->nullable();
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
        Schema::dropIfExists('hub_files');
    }
};
