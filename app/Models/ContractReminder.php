<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractReminder extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'contract_id',
        'notified_by',
        'days_before_expiry',
        'expire_date',
    ];
    
}
