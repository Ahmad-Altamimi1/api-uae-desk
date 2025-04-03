<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFtaMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'document_name',
        'file_path',
        'start_date',
        'expire_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
