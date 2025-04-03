<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFtaFieldsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('fta_user_name')->nullable();
            $table->string('fta_password')->nullable();
            $table->string('fta_refrence')->nullable();
            $table->string('transaction_refrence_number')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
}
