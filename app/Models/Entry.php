<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'date', 'amount',"description","type"];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
