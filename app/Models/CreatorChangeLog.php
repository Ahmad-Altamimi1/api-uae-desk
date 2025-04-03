<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreatorChangeLog extends Model
{
    use HasFactory;

    protected $table = 'creator_change_logs';

    protected $fillable = [
        'customer_id',
        'old_creator_id',
        'new_creator_id',
        'changed_by',
    ];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
