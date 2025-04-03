<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'document_type',
        'document_details',
        'requested_by',
        'is_viewed', // Add is_viewed to the fillable attributes
    ];

    // Relationship to the Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship to the User who requested the document
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
