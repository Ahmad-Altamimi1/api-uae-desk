<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('upcoming_payment_reminders', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('upcoming-payments_id')->constrained('entries')->cascadeOnDelete();
        //     $table->foreignId('notified_by')->constrained('users')->cascadeOnDelete();
        //     $table->string('reminder_type');
        //     $table->date('upcoming-payments_date');
        //     $table->timestamps();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('upcoming_payment_reminders');
    }
};
