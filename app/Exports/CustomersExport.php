<?php
namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    /**
     * Fetch the data for export.
     */
    public function collection()
    {
        // Get customers with their related branch and service data
        return Customer::with('branch', 'service') // Eager load the branch and service
            ->get()
            ->map(function ($customer) {
                // Modify the customer data to replace branch_id with the branch name
                return [
                    'customer_code' => $customer->customer_code,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'phone_number' => $customer->phone_number,
                    'email' => $customer->email,
                    'branch_name' => $customer->branch ? $customer->branch->branch_name : null, // Use branch name
                    'service' => $customer->service ? $customer->service->name :  null
                    //'status' => $customer->status,
                ];
            });
    }

    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return [
            'Customer Code',
            'First Name',
            'Last Name',
            'Phone Number',
            'Email',
            'Branch Name', // Update heading to 'Branch Name'
            'Service'
           // 'Status',
        ];
    }
}
