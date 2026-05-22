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
        Schema::create('history_deposit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('invoice');
            $table->decimal('amount', 15, 2);
            $table->enum('status_payment', ['Pending', 'Error', 'Cancel', 'Success'])->default('Pending');
            $table->json('detail_transaction')->nullable();
            $table->timestamp('create_at')->nullable();
            $table->timestamp('update_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_deposit');
    }
};
