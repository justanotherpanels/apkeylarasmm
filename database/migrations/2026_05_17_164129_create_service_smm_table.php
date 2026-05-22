<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_smm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_category_smm');
            $table->unsignedBigInteger('id_api_smm');
            $table->string('name_service');
            $table->string('pid')->nullable();
            $table->integer('min_order')->default(0);
            $table->integer('max_order')->default(0);
            $table->decimal('price_api', 15, 2)->default(0);
            $table->decimal('price_sale', 15, 2)->default(0);
            $table->decimal('price_reseller', 15, 2)->default(0);
            $table->enum('type', ['Default', 'Package', 'Custom Comments', 'Poll', 'Subscriptions'])->default('Default');
            $table->text('desc')->nullable();
            $table->boolean('refill')->default(false);
            $table->enum('status', ['Active', 'Not-Active'])->default('Active');
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();

            $table->foreign('id_category_smm')->references('id')->on('category_smm')->onDelete('cascade');
            $table->foreign('id_api_smm')->references('id')->on('api_smm')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_smm');
    }
};
