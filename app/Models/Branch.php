<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['branch_name', 'address', 'phone_number', 'email', 'location_id', 'latitude',
    'longitude',];



    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function users()
{
    return $this->belongsToMany(User::class);
}

}
