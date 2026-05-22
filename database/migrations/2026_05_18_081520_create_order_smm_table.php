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
        Schema::create('order_smm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_service_smm')->nullable();
            $table->unsignedBigInteger('id_api_smm')->nullable();
            $table->string('sid')->nullable(); // API Order ID
            $table->string('invoice')->unique();
            $table->text('target');
            $table->integer('amount'); // Jumlah order / quantity
            $table->decimal('price_api', 15, 2)->default(0);
            $table->decimal('price_sale', 15, 2)->default(0);
            $table->decimal('price_reseller', 15, 2)->default(0);
            $table->enum('status_order', ['Pending', 'In Progres', 'Partial', 'Cancel', 'Error', 'Success', 'Finish'])->default('Pending');
            $table->integer('start_count')->default(0);
            $table->integer('remains')->default(0);
            $table->boolean('refill')->default(false);
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();

            // Foreign Keys
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_service_smm')->references('id')->on('service_smm')->onDelete('set null');
            $table->foreign('id_api_smm')->references('id')->on('api_smm')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_smm');
    }
};
