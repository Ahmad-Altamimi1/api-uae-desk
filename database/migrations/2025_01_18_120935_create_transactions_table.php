<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('transaction_refrence_number')->unique();
            $table->enum('payment_method', ['stripe', 'paypal', 'cash', 'bank_transfer']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'successful', 'failed', 'paid'])->default('pending');
            $table->timestamps();

            // Foreign Key Constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
