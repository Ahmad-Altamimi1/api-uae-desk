<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

   public  function getAllBranches()
   {
        return $this->hasMany(Branch::class, 'location_id','id');
    }
   
}
