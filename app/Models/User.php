<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, Loggable;

    // protected $guard = 'admin';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'image',
        'status',
        'branch_id',
        'location_id',
        "is_location_flexible",
        'created_by',
        'updated_by',
        'shift_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        "created_at",
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Return all roles of the current user by their names
     *
     * @return \Illuminate\Database\Eloquent\Collection|Role[]
     */
    public function getRoleCodes()
    {
        $user = Auth::user();
        return $roles = Role::where('name', $user->getRoleNames())->get();
    }

    public function getSuperVisorUsers()
    {
        $Users = User::all();
        $superVisors = [];
        foreach ($Users as $user) {
            if ($user . getRoleNames() == "supervisor") {
                array_push($superVisors, $user);
            }
        }
        return $superVisors;
    }
    public function getNonAdminUsers()
    {
        return User::whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['Admin', 'Super Admin']);
        })->get();
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public static function generateUniqueId($prefix)
    {
        // Extract initials from the branch name
        // $prefix = collect(explode(' ', $branchName))
        //     ->map(function ($word) {
        //         return strtoupper(substr($word, 0, 1));
        //     })
        //     ->join('');

        // Find the latest customer code in the database
        $lastCustomerCode = Customer::where('customer_code', 'LIKE', "$prefix-%")
            ->orderBy('customer_code', 'desc')
            // ->where('created_by', Auth::user()->id)
            ->value('customer_code');

        // Extract the numeric part from the last customer code
        $lastNumber = $lastCustomerCode ? (int) substr($lastCustomerCode, strlen($prefix) + 1) : 0;

        // Increment the number for the new code
        $newNumber = $lastNumber + 1;

        // Format the number with leading zeros (6 digits)
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        // Combine prefix and formatted number
        return $prefix . '-' . $formattedNumber;
    }

    public static function generateInvoiceId($prefix = 'INV')
    {
        $prefix = collect(explode(' ', $prefix))
            ->map(function ($word) {
                return strtoupper(substr($word, 0, 1));
            })
            ->join('');

        // Find the latest invoice ID in the database
        $lastInvoiceId = customer::where('invoice_number', 'LIKE', "$prefix-%")
            ->orderBy('invoice_number', 'desc')
            // ->where('created_by', Auth::user()->id)
            ->value('invoice_number');

        // Extract the numeric part from the last invoice ID
        $lastNumber = $lastInvoiceId ? (int) substr($lastInvoiceId, strlen($prefix) + 1) : 0;

        // Increment the number for the new invoice ID
        $newNumber = $lastNumber + 1;

        // Format the number with leading zeros (6 digits)
        $formattedNumber = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        // Combine prefix and formatted number
        return $prefix . '-' . $formattedNumber;
    }


    /**
     * Get the customers created by the user.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function createBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function generateSerialNumber()
    {
        // Get the highest serial number in the table
        $lastSerialNumber = Customer::max('serial_number');

        // Increment the serial number
        return $lastSerialNumber ? $lastSerialNumber + 1 : 1;
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
