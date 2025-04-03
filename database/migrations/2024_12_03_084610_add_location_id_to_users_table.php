<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add location_id column with foreign key
            $table->unsignedBigInteger('location_id')->nullable()->after('branch_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            // Add created_by and updated_by columns
            $table->unsignedBigInteger('created_by')->nullable()->after('location_id');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Add foreign keys for created_by and updated_by referencing the users table
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['location_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn(['location_id', 'created_by', 'updated_by']);
        });
    }
}
