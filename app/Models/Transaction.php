<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'transaction_refrence_number',
        'payment_method',
        'amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
