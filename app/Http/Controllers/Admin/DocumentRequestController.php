<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\DocumentRequest;
class DocumentRequestController extends Controller
{
    
    public function getRequests(Customer $customer)
{
    $requests = DocumentRequest::where('customer_id', $customer->id)
        ->select('document_type', 'document_details')
        ->get();

    return response()->json($requests);
}
}
