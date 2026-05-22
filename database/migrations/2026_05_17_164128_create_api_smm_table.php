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
        Schema::create('api_smm', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('url')->nullable();
            $table->string('api_key')->nullable();
            $table->enum('status', ['Active', 'Not-Active'])->default('Active');
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_smm');
    }
};
