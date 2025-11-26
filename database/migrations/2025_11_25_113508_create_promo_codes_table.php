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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('valid_date'); // Start date
            $table->date('expire_date'); // End date
            $table->decimal('discount_percentage', 5, 2); // Percentage (e.g., 10.50 for 10.5%)
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->integer('usage_limit')->nullable(); // Optional: max number of times code can be used
            $table->integer('used_count')->default(0); // Track how many times it's been used
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
        Schema::dropIfExists('promo_codes');
    }
};
