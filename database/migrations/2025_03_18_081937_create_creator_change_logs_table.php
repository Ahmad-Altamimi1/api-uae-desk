<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creator_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('old_creator_id');
            $table->unsignedBigInteger('new_creator_id');
            $table->timestamps();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('changed_by');
            // $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            // $table->foreignId('changed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('creator_change_logs');
    }
}
