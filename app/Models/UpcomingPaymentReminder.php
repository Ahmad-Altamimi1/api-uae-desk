<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingPaymentReminder extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'upcoming-payments_id',
        'notified_by',
        'reminder_type',
        'upcoming-payments_date',
    ];
}
