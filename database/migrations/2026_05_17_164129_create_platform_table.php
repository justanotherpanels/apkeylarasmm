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
        Schema::create('platform', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon_imagekit_url')->nullable();
            $table->unsignedBigInteger('id_category_smm')->nullable();
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();

            $table->foreign('id_category_smm')->references('id')->on('category_smm')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform');
    }
};
