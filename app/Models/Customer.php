<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'first_name',
        'last_name',
        'updated_by',
        'business_name',
        'phone_number',
        'second_number',
        'email',
        'address',
        'status',
        'created_by',
        'customer_code',
        'invoice_number',
        'price',
        'portal_email',
        'portal_password',
        'tax_id',
        'review_by',
        'invoice_pdf_url',
        'document_details',
        'vat_value',
        'serial_number',
        'branch_id',
        'transaction_refrence_number',
        'fta_refrence',
        'fta_password',
        'fta_user_name',
        'payment_method',
        'gmail_user_name',
        'gmail_password',
        'submitted_for_verification_at',
        'expert_submitted_at',
        'supervisor_approved_at'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function media()
    {
        return $this->hasMany(CustomerMedia::class)->selectRaw('*, (CASE WHEN file_path LIKE \'%.pdf\' THEN \'pdf\' WHEN file_path LIKE \'%.docx\' THEN \'docx\' WHEN file_path LIKE \'%.jpg\' THEN \'jpg\' WHEN file_path LIKE \'%.png\' THEN \'png\' WHEN file_path LIKE \'%.txt\' THEN \'txt\' WHEN file_path LIKE \'%.csv\' THEN \'csv\' ELSE \'unknown\' END) as file_type');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function review()
    {
        return $this->belongsTo(User::class, 'review_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function ftamedia()
    {
        return $this->hasMany(CustomerFtaMedia::class);
    }

public function getDataEntryTimeAttribute()
{
    $start = Carbon::parse($this->created_at);
    $end = $this->submitted_for_verification_at 
        ? Carbon::parse($this->submitted_for_verification_at)
        : null;

    return [
        'start' => $start,
        'end' => $end,
        'total' => $end ? $start->diffInMinutes($end) : null,
    ];
}


    public function getExpertVerificationTimeAttribute()
    {
        $start =$this->submitted_for_verification_at ? Carbon::parse($this->submitted_for_verification_at):null;
        $end= $this->expert_submitted_at 
        ? Carbon::parse($this->expert_submitted_at)
        : null;
        return 
            [
                'start' => $start,
                'end' =>$end,
                'total' =>  $end ? $start->diffInMinutes($end) : null
            ];
    }

    public function getSupervisorApprovalTimeAttribute()
    {
           $start =$this->expert_submitted_at? Carbon::parse($this->expert_submitted_at):null;
        $end= $this->supervisor_approved_at 
        ? Carbon::parse($this->supervisor_approved_at)
        : null;
        return [
            'start' => $start,
            'end' =>$end,
            'total' =>  $end ? $start->diffInMinutes($end) : null
        ];
    }

    public function getTotalVerificationTimeAttribute()
    {
        return $this->supervisor_approved_at
            ? Carbon::parse($this->created_at)->diffInMinutes($this->supervisor_approved_at)
            : null;
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'customer_services')->withPivot('price'); // Include price in pivot;
    }
    public function upcomingPayments()
    {
        return $this->hasMany(Entry::class);
    }
    public function creatorChangeLogs()
    {
        return $this->hasMany(CreatorChangeLog::class);
    }
}
