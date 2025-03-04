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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reg_number');
            $table->string('name');
            $table->string( 'email' );
            $table->string( 'phone_number' );
            $table->string('paymentStatus')->nullable();
            $table->string('amount')->nullable();
            $table->string('faculty');
            $table->string('department');
            $table->string('amount_settled')->nullable();
            $table->string('tx_ref');
            $table->string('txr_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
