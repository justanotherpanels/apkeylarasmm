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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('password');
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->enum('level', ['Member', 'Admin'])->default('Member');
            $table->enum('status', ['Active', 'Not-Active'])->default('Active');
            $table->string('api_key')->nullable();
            $table->boolean('is_seller')->default(false);
            $table->enum('koneksi', ['Mobile', 'API', 'Web'])->default('Web');
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
