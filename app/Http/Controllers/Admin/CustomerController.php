<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\CustomerMedia;
use Storage;
use Toastr;
use App\Models\User;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DocumentRequest;
use App\Mail\TaxIdAddedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use App\Models\CustomerFtaMedia;
use App\Mail\PaymentLinkEmail;
use App\Models\CreatorChangeLog;
use App\Models\Entry as ModelsEntry;
use App\Models\Transaction;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CustomerController extends Controller
{
    function __construct()
    {
        //TODO
        // $this->middleware('auth');
        // $this->middleware('permission:customers-list', ['only' => ['index', 'store']]);
        // $this->middleware('permission:customers-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:customers-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:customers-delete', ['only' => ['destroy']]);
        // $this->middleware('role:supervisor');
        // date_default_timezone_set('Asia/Dubai');
    }
    public function index(Request $request)
    {
            if (auth()->user()->hasRole('operator')||true) {
                $customers = Customer::with(['services', 'branch'])
                    ->where('created_by', auth()->id())
                    ->where('status', '!=', 3)
                    ->get();
            } elseif (auth()->user()->hasRole('expert')) {
                $customers = Customer::with(['services', 'branch'])
                    ->where('status', '!=', 0)
                    ->get();
            } else {
                $customers = Customer::with(['services', 'branch'])
                    ->orderBy('updated_at', 'desc')
                    ->get();
            }
    
            return response()->json($customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'business_name' => $customer->business_name,
                    'phone_number' => $customer->phone_number,
                    'email' => $customer->email,
                    'customer_code' => $customer->customer_code,


                    'branch' => $customer->branch ? $customer->branch->branch_name : null,
                    'services' => $customer->services->pluck('name'),
                    'created_reviewed_by' => (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('supervisor'))
                        ? [
                            'created_by' => $customer->creator ? $customer->creator->name : 'N/A',
                            'reviewed_by' => $customer->review ? $customer->review->name : 'N/A',
                        ] : null,
                    'status' =>$customer->status,
                    // 'actions' => [
                    //     'view' => (Gate::check('customers-view') || auth()->user()->hasRole('supervisor'))
                    //         ? route('customers.show', $customer->id) : null,
                    //     'edit' => (Gate::check('customers-edit') || auth()->user()->hasRole('supervisor')) &&
                    //         (auth()->user()->hasRole('operator') && in_array($customer->status, [0, 1]) || auth()->user()->hasRole('supervisor'))
                    //         ? route('customers.edit', $customer->id) : null,
                    //     'delete' => Gate::check('customers-delete')
                    //         ? route('customers.destroy', $customer->id) : null,
                    //     'upload_media' => (Gate::check('customers-upload-media') || auth()->user()->hasRole('supervisor'))
                    //         ? route('customers.media', $customer->id) : null,
                    //     'print_invoice' => route('invoices.view', ['id' => $customer->id]),
                    //     'account_statement' => (Gate::check('customers-account') || auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin'))
                    //         ? route('customers.account-statement', $customer->id) : null,
                    // ],
                    'document_indicator' => auth()->user()->hasRole('operator') && DocumentRequest::where('customer_id', $customer->id)->where('is_viewed', false)->exists(),
                ];
            }));
    
    }
    


    public function create()
    {
        $services = Service::all();
        $settings = Setting::first(); // Assuming settings is a single record table
        $branches = Branch::all();
        return view('admin.customers.create', compact('services', 'settings', 'branches'));
    }

    

    public function store(Request $request)
    {
     
        
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|array',  
            'service_id.*' => 'exists:services,id',
            // 'service_price' => 'required|array',
            // 'service_price.*' => 'numeric|min:0',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'second_number' => 'nullable|string|max:15',
            'email' => 'required|email',
            'address' => 'nullable',
            'status' => 'nullable|numeric',
            'tax_id' => 'nullable',
            'price' => 'required|numeric',
            'vat_value' => 'nullable',
            'branch_id' => 'required|exists:branches,id',
            'transaction_refrence_number' => 'nullable',
            'fta_refrence' => 'nullable',
            'fta_password' => 'nullable',
            'fta_user_name' => 'nullable',
            'payment_method' => 'nullable|string',
            'gmail_user_name' => 'nullable',
            'gmail_password' => 'nullable',
            'entries' => 'nullable|array', // Add a rule if needed
        ]);
        
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'errors' => $validator->errors()
            ], 422));
        }
        
        $validated = $validator->validated();
        

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } 	
        if ($validated['payment_method'] === 'stripe') {
            $validated['transaction_refrence_number'] = null;
        }
        $validated['created_by'] = auth()->id();
        $branchName = 'HLA';
        $validated['customer_code'] = User::generateUniqueId('HLA');
        $validated['invoice_number'] = User::generateInvoiceId($branchName);
        $validated['serial_number'] = User::generateSerialNumber();

        $customer = Customer::create($validated);
        if (isset($request['entries'])) {
            foreach ($validated['entries'] as $upcomingPayment) {
                ModelsEntry::create([
                    'customer_id' => $customer->id,
                    'date' => $upcomingPayment['date'],
                    'amount' => $upcomingPayment['amount'],
                    'description' => $upcomingPayment['description'] ?? "",
                ]);
            }
        }
        $servicesWithPrices = [];
        foreach ($request->service_id as $serviceId) {
            $servicesWithPrices[$serviceId] = ['price' =>  0, 'created_at' => now(), 'updated_at' => now()];
        }
        $customer->services()->attach($servicesWithPrices);
        // ✅ If payment method is Stripe, generate a payment link
        if ($validated['payment_method'] === 'stripe') {
            Stripe::setApiKey(config('services.stripe.secret'));
            // ✅ Calculate total amount with VAT
            $price = $validated['price'];
            $vatValue = $validated['vat_value'] ?? 0;
            $vatAmount = $price * ($vatValue / 100);
            $totalAmount = round(($price + $vatAmount) * 100); // Ensure amount is in cents (integer)

            // ✅ Create Stripe Checkout Session (Payment Link)
            $session = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'customer_email' => $validated['email'], // Pre-fill customer's email
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'AED',
                            'product_data' => [
                                'name' => 'Service Payment - ' . ($service->name ?? 'Unknown Service'),
                                'description' => 'Invoice #: ' . $validated['invoice_number'],
                            ],
                            'unit_amount' => $totalAmount, // Amount in cents
                        ],
                        'quantity' => 1,
                    ]
                ],
                'metadata' => [
                    'customer_id' => $customer->id, // Ensure customer ID is set
                    'invoice_number' => $validated['invoice_number'],
                ],
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
            ]);

            $paymentLink = $session->url;

            // ✅ Save transaction details (pending)
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'payment_method' => 'stripe',
                'amount' => $totalAmount / 100, // Convert back to AED
                'status' => 'pending',
                'transaction_refrence_number' => $session->id,
            ]);

            // ✅ Send email with payment link
            Mail::to($validated['email'])->send(new PaymentLinkEmail($paymentLink));

            Log::info('✅ Stripe Payment Link Sent', ['customer_id' => $customer->id, 'payment_link' => $paymentLink]);

            Toastr::success(__('Customer added successfully. Payment link has been emailed.'));
            return auth()->user()->hasRole('supervisor') || auth()->user()->hasRole("Admin") || auth()->user()->hasRole('Super Admin') ? redirect()->route('branches.allBranchesData') : redirect()->route('customers.index');
        }

        return response()->json(['message' => 'Customer added successfully.',"id"=>$customer->id], 201);
    }

    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $services = Service::all();
            $settings = Setting::first(); 
            $branches = Branch::all();
            $entries = $customer->entries;
            // Retrieve selected service IDs and their prices for the customer
            $selectedServices = $customer->services()->pluck('services.id')->toArray();
            $selectedServicePrices = $customer->services()->pluck('customer_services.price', 'services.id')->toArray();

            return response()->json([
                'status' => true,
                'data' => [
                    'customer' => $customer,
                    'services' => $services,
                    'branches' => $branches,
                    'settings' => $settings,
                    'selectedServices' => $selectedServices,
                    'selectedServicePrices' => $selectedServicePrices,
                    "entries" => $entries,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('Customer not found.')
            ], 404);
        }
    }



    public function update(Request $request)
    {
      
        try {
              $id = $request->id;
            // Find the customer by ID
            $customer = Customer::findOrFail($id);
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|array',  
                'service_id.*' => 'exists:services,id',
                // 'service_price' => 'required|array',
                // 'service_price.*' => 'numeric|min:0',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:15',
                'second_number' => 'nullable|string|max:15',
                'email' => 'required|email',
                'address' => 'nullable',
                'status' => 'nullable|numeric',
                'tax_id' => 'nullable',
                'price' => 'required|numeric',
                'vat_value' => 'nullable',
                'branch_id' => 'required|exists:branches,id',
                'transaction_refrence_number' => 'nullable',
                'fta_refrence' => 'nullable',
                'fta_password' => 'nullable',
                'fta_user_name' => 'nullable',
                'payment_method' => 'nullable|string',
                'gmail_user_name' => 'nullable',
                'gmail_password' => 'nullable',
                'entries' => 'nullable|array', // Add a rule if needed
            ]);
            
            if ($validator->fails()) {
                throw new HttpResponseException(response()->json([
                    'errors' => $validator->errors()
                ], 422));
            }
            
            $validated = $validator->validated();
            
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            } 	
            $existingEntries = ModelsEntry::where('customer_id', $customer->id)->pluck('id')->toArray();
            if (auth()->user()->hasRole('supervisor')) {
                $validated['updated_by'] = auth()->id();
            }

            $newEntryIds = [];
            if (isset($validated['entries'])) {

                foreach ($validated['entries'] as $entry) {
                    if (isset($entry['id']) && in_array($entry['id'], $existingEntries)) {
                        // Update existing entry
                        ModelsEntry::where('id', $entry['id'])->update([
                            'date' => $entry['date'],
                            'amount' => $entry['amount'],
                            'description' => $entry['description'],

                        ]);
                        $newEntryIds[] = $entry['id']; // Mark it as updated
                    } else {
                        // Create new entry

                        $newEntry = ModelsEntry::create([
                            'customer_id' => $customer->id,
                            'date' => $entry['date'],
                            'amount' => $entry['amount'],
                            'description' => $entry['description'] ?? "",

                        ]);
                        $newEntryIds[] = $newEntry->id;
                    }
                }
            }
            // Delete removed entries
            ModelsEntry::where('customer_id', $customer->id)
                ->whereNotIn('id', $newEntryIds)
                ->delete();

           
            $servicesWithPrices = [];
            foreach ($request->service_id as $serviceId) {
                $servicesWithPrices[$serviceId] = ['price' => $request->service_price[$serviceId] ?? 0];
            }

            // Sync service prices for the customer
            $customer->services()->sync($servicesWithPrices);
            //}
            // Update the customer record
            $customer->update($validated);


            // Success message
            return response()->json(['message' => __('Customer updated successfully.')], 200);
        } catch (ValidationException $e) {
            // Catch validation errors specifically
            // Log::error('Validation error updating customer: ', [
            //     'errors' => $e->errors(),  // Get the validation errors
            //     'user_id' => auth()->id(),
            //     'customer_id' => $id,  // Log the customer ID for context
            // ]);

            // Display validation errors to the user
            return response()->json(['message' => __('The provided data is invalid.'), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            // // Log any other exception
            // Log::error('Error updating customer: ', [
            //     'message' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'user_id' => auth()->id(),
            //     'customer_id' => $id,
            // ]);

            // Display a general error message
            return response()->json(['message' => __('Error updating customer.')], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);

            // Detach all associated services
            $customer->services()->detach();

            // Delete the customer
            $customer->delete();

            Toastr::success(__('Customer deleted successfully.'));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Toastr::error(__('Error deleting customer.'));
            return response()->json(['success' => false], 500);
        }
    }


    public function show($id)
    {
        
        $customer = Customer::with(['branch', 'services', 'entries', 'media',"ftamedia"])->find($id);
        if (!$customer) {
            return response()->json(['error' => __('Customer not found.')], 404);
        }
        // Check if the user has the appropriate role
        if (!(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Supervisor'))) {
            return response()->json(['error' => __('Customer not found or access denied.')], 404);
        }
        
        $selectedServices = $customer->services()->pluck('services.id')->toArray();
        $entries = $customer->entries;
        $employees = User::role('operator')->get();
        $customerDetails = $customer->document_details ? json_decode($customer->document_details, true) : [];
        $getProcessTime = $this->getProcessTime($customer->id);
        return response()->json([
            'customer' => $customer,
            'customerDetails' => $customerDetails,
            'processTime' => $getProcessTime,
            'entries' => $entries,
            'selectedServices' => $selectedServices,
            'employees' => $employees,
        ]);
    }

    public function servicesDetails($id)
    {
        
        $customer = Customer::findOrFail($id);
        if (!$customer) {
            return response()->json(['error' => __('Customer not found.')], 404);
        }
        // Check if the user has the appropriate role
        if (!(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Supervisor'))) {
            return response()->json(['error' => __('Customer not found or access denied.')], 404);
        }
        
       
        $servicesDetails = $customer->document_details ? json_decode($customer->document_details, true) : [];
        return response()->json([
            'servicesDetails' => $servicesDetails,
        ]);
    }

        public function groupedMedia($id){
            $customer = Customer::findOrFail($id);
            $groupedMedia = $customer->media->groupBy('document_name');
            return response()->json([
                'groupedMedia' => $groupedMedia,
        ]);

        }
    public function media($id)
    {
        $customer = Customer::findOrFail($id); // Retrieve customer by ID
        return view('admin.customers.media', compact('customer'));
    }
    public function storeMedia(Request $request)
    {
        Log::info('Received request to store media.', ['request' => $request->all()]);

        try {
            $request->validate([
                'id' => 'required|exists:customers,id',
                'document_name' => 'required|string|max:255',
                'media.*' => 'file|mimes:webp,jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,csv|max:5120',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation error while storing media.', ['errors' => $e->errors()]);
            return response()->json(['message' => __('The provided data is invalid.'), 'errors' => $e->errors()], 422);
        }
    
        $customer = Customer::findOrFail($request->input('id'));
    
        try {
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $path = $file->store('uploads/customers/' . $customer->id, 'public');
    
                    CustomerMedia::create([
                        'customer_id' => $customer->id,
                        'document_name' => $request->input('document_name') ?: $fileName,
                        'file_path' => $path,
                    ]);
                }
            }
    
            Log::info('Media uploaded successfully for customer.', ['customer_id' => $customer->id]);
            return response()->json(['success' => true, 'message' => __('Media uploaded successfully.')]);
        } catch (\Exception $e) {
            Log::error('Error uploading media.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => __('Error uploading media.'),
                'error' => $e->getMessage(), // optional for debugging
            ], 500);
        }
    }
    

    public function deleteMedia(Request $request)
    {
        $id = $request->id;
        $media = CustomerMedia::findOrFail($id);

        Log::info('Received request to delete media.', ['media_id' => $id]);

        try {
            // Delete file from storage
            Storage::disk('public')->delete($media->file_path);

            // Delete record from the database
            $media->delete();

            Log::info('Media deleted successfully.', ['media_id' => $id]);

            return response()->json(['success' => true, 'message' => __('Media deleted successfully.')]);
        } catch (\Exception $e) {
            Log::error('Error deleting media.', ['error' => $e->getMessage(), 'media_id' => $id]);

            return response()->json(['success' => false, 'message' => __('Error deleting media.')], 500);
        }
    }

    public function submitForVerification(Request $request) //used
    {
        $id = $request->id;
        $customer = Customer::findOrFail($id);

        try {
            // Update the status to "In Process" or any other desired value
            $customer->status = 1; // Assuming 1 represents "In Process"
            $customer->save();
            $customer->update([
                'submitted_for_verification_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => __('Customer submitted for verification successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error submitting customer for verification.')], 500);
        }
    }

    public function submitForReview(Request $request, $customer)
    {

        $validated = $request->validate([
            'fta_user_name' => 'required|string',
            'fta_password' => 'required',
            'gmail_user_name' => 'nullable|email',
            'gmail_password' => 'nullable|string',
        ]);

        try {
            // Ensure $customer is an ID and retrieve the customer model
            $customer = Customer::findOrFail($customer); // Assuming $customer is an object with an ID, adjust as needed.

            // Update the customer fields
            $customer->fta_user_name = $request->fta_user_name;
            $customer->fta_password = $request->fta_password;
            $customer->gmail_user_name = $request->gmail_user_name;
            $customer->gmail_password = $request->gmail_password;
            $customer->status = 2;
            $customer->review_by = Auth::id(); // Auth::id() is cleaner than Auth::user()->id
            $customer->save();
            $customer->update([
                'expert_submitted_at' => now()
            ]);
            Toastr::success(__('Customer submitted for review successfully.'));
            return redirect()->route('branches.allBranchesData');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error submitting customer for review: ' . $e->getMessage(), [
                'customer_id' => $customer->id ?? null,
                'request_data' => $request->all(),
            ]);

            Toastr::error(__('Error submitting customer for review.'));
            return redirect()->back()->withInput(); // Preserve input on error
        }
    }

    public function requestDocument(Request $request)
    {

        try {
            $id = $request->id;
            // Validate the request data
            $validated = $request->validate([
                'document_type' => 'required|string',
                'document_details' => 'nullable|string',
            ]);

            // Store the document request details in the database
            $newDocumentRequest = DocumentRequest::create([
                'customer_id' => $id,
                'document_type' => $validated['document_type'],
                'document_details' => $validated['document_details'],
                'requested_by' => Auth::user()->id, // Store the ID of the authenticated user
                'is_viewed' => false, // Default is not viewed
            ]);

            // Update the customer's status to Pending
            $customer = Customer::findOrFail($id);
            $customer->update([
                'status' => 0, // Pending
            ]);

            // Success response
            return response()->json(['success' => true, 'message' => __('Document request submitted successfully.'), 'newDocumentRequestId' => $newDocumentRequest->id], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error requesting document.')], 500);
        }
    }

    public function addTaxId(Request $request, Customer $customer)
    {
        $request->validate([
            'tax_id' => 'required|string|max:255'
        ]);

        try {
            $customer->tax_id = $request->tax_id;
            $customer->status = 3;
            $customer->save();
            $customer->update([
                'supervisor_approved_at' => now()
            ]);
            $settings = Setting::first();
            $companyEmail = $settings->email ?? 'N/A';
            $companyAddress = $settings->address ?? 'N/A';
            $companyPhone = $settings->phone ?? 'N/A';

            // Send email to customer
            try {

                if ($request->has('send_email')) {
                    Mail::to($customer->email)->send(new TaxIdAddedMail(
                        $customer,
                        $request->tax_id,
                        $companyEmail,
                        $companyPhone,
                        $companyAddress
                    ));
                }
            } catch (\Exception $e) {
                // Log the exception for debugging purposes
                Log::error('Failed to send email: ' . $e->getMessage());
            }
            Toastr::success(__('Tax ID added successfully. An email has been sent to the customer.'));
            return redirect()->route('branches.allBranchesData');
        } catch (\Exception $e) {
            Toastr::error(__('Error adding Tax ID.'));
            return redirect()->back();
        }
    }


    public function saveDocumentDetails(Request $request) //Done
    {
        $validator = Validator::make($request->all(), [
            'profile_name_en' => 'nullable|string|max:255',
            'profile_name_ar' => 'nullable|string|max:255',
            'preferred_language' => 'nullable|string',
            'communication_channel' => 'nullable|string',
            'emirates_id' => 'nullable|array',
            'passport' => 'nullable|array',
            'tax_certificate' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors.', 'errors' => $validator->errors()], 422);
        }

        try {
            $customerId = $request->id; 
            $customer = Customer::findOrFail($customerId);
            $customer->document_details = json_encode($request->all());
            $customer->save();

            return response()->json(['success' => true, 'message' => 'Details saved successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save details.'], 500);
        }
    }

    public function invoiceView($id)
    {
        // Fetch customer using the given ID or throw a 404 error if not found
        $customer = Customer::with('service')->find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
        }

        // Ensure that a related service exists for the customer
        $services = $customer->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->pivot->price,
            ];
        });

        // Invoice and Receipt details
        $invoiceNumber = $customer->invoice_number;
        $receiptNumber = $customer->serial_number;
        $created = auth()->user();
        $date = now()->format('Y-m-d'); // Invoice Date
        $receiptDate = now()->format('Y-m-d'); // Receipt Date

        // Fetch VAT value from customer or default to 0
        $vatValue = $customer->vat_value ?? 0;

        // Calculate VAT and total amounts
        $servicePrice = $customer->price ?? 0;
        $vatAmount = $servicePrice * ($vatValue / 100);
        $totalAmount = $servicePrice + $vatAmount;

        // Fetch settings for company details
        $settings = Setting::first();
        $companyEmail = $settings['email'] ?? 'N/A';
        $companyAddress = $customer->branch ? $customer->branch->branch_name : 'N/A';
        $companyPhone = $settings['phone'] ?? 'N/A';
        $logo = $settings['website_logo_small'] ?? 'N/A';


        $paymentMethodsReadable = [
            'by_machine' => 'By Machine',
            'by_link' => 'By Link',
            'cashier' => 'Cashier',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer'
        ];

        // Get readable payment method or default to 'N/A'
        $payment_method = $paymentMethodsReadable[$customer->payment_method] ?? 'N/A';

        // Return the invoice view
        return response()->json([
            'customer' => $customer,
            'services' => $services,
            'invoiceNumber' => $invoiceNumber,
            'receiptNumber' => $receiptNumber,
            'created' => $created,
            'date' => $date,
            'receiptDate' => $receiptDate,
            'vatValue' => $vatValue,
            'vatAmount' => $vatAmount,
            'totalAmount' => $totalAmount,
            'companyEmail' => $companyEmail,
            'companyAddress' => $companyAddress,
            'companyPhone' => $companyPhone,
            'logo' => $logo,
            'payment_method' => $payment_method
        ]);
    }

    public function printInvoice($id)
    {
        // Fetch customer using the given ID or throw a 404 error if not found
        $customer = Customer::with('service')->findOrFail($id);

        // Ensure that a related service exists for the customer
        $services = $customer->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->pivot->price,
            ];
        });
        // Invoice and Receipt details
        $invoiceNumber = $customer->invoice_number;
        $receiptNumber = $customer->serial_number;
        $created = auth()->user();
        $date = now()->format('Y-m-d'); // Invoice Date
        $receiptDate = now()->format('Y-m-d'); // Receipt Date

        // Fetch VAT value from customer or default to 0
        $vatValue = $customer->vat_value ?? 0;

        // Calculate VAT and total amounts
        $servicePrice = $customer->price ?? 0;
        $vatAmount = $servicePrice * ($vatValue / 100);
        $totalAmount = $servicePrice + $vatAmount;

        // Fetch settings for company details
        $settings = Setting::first();
        $companyEmail = $settings['email'] ?? 'N/A';
        $companyAddress = $customer->branch ? $customer->branch->branch_name : 'N/A';
        $companyPhone = $settings['phone'] ?? 'N/A';
        $logo = $settings['website_logo_small'] ?? 'N/A';
        $paymentMethodsReadable = [
            'by_machine' => 'By Machine',
            'by_link' => 'By Link',
            'cashier' => 'Cashier',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer'
        ];

        // Get readable payment method or default to 'N/A'
        $payment_method = $paymentMethodsReadable[$customer->payment_method] ?? 'N/A';

        // Return the invoice view
        return view('invoice.invoice-single', compact(
            'customer',
            'services',
            'invoiceNumber',
            'receiptNumber',
            'created',
            'date',
            'receiptDate',
            'vatValue',
            'vatAmount',
            'totalAmount',
            'companyEmail',
            'companyAddress',
            'companyPhone',
            'logo',
            'payment_method'
        ));
    }


    public function printReceipt($id)
    {
        // Fetch customer using the given ID or throw a 404 error if not found
        $customer = Customer::with('service')->findOrFail($id);

        // Ensure that a related service exists for the customer
        $services = $customer->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->pivot->price,
            ];
        });

        // Invoice and Receipt details
        $invoiceNumber = $customer->invoice_number;
        $receiptNumber = $customer->serial_number;
        $created = auth()->user();
        $date = now()->format('Y-m-d'); // Invoice Date
        $receiptDate = now()->format('Y-m-d'); // Receipt Date

        // Fetch VAT value from customer or default to 0
        $vatValue = $customer->vat_value ?? 0;

        // Calculate VAT and total amounts
        $servicePrice = $customer->price ?? 0;
        $vatAmount = $servicePrice * ($vatValue / 100);
        $totalAmount = $servicePrice + $vatAmount;

        // Fetch settings for company details
        $settings = Setting::first();
        $companyEmail = $settings['email'] ?? 'N/A';
        $companyAddress = $customer->branch ? $customer->branch->branch_name : 'N/A';
        $companyPhone = $settings['phone'] ?? 'N/A';
        $logo = $settings['website_logo_small'] ?? 'N/A';
        $paymentMethodsReadable = [
            'by_machine' => 'By Machine',
            'by_link' => 'By Link',
            'cashier' => 'Cashier',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer'
        ];

        // Get readable payment method or default to 'N/A'
        $payment_method = $paymentMethodsReadable[$customer->payment_method] ?? 'N/A';

        // Return the invoice view
        return view('invoice.receipt', compact(
            'customer',
            'services',
            'invoiceNumber',
            'receiptNumber',
            'created',
            'date',
            'receiptDate',
            'vatValue',
            'vatAmount',
            'totalAmount',
            'companyEmail',
            'companyAddress',
            'companyPhone',
            'logo',
            'payment_method'
        ));
    }
    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }

    public function storeFtaMedia(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'document_name' => 'required|string|max:255',
                'fta_document' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
                'start_date' => 'required|date|before:expire_date',
                'expire_date' => 'required|date',
            ]);

            $customer = Customer::findOrFail($id);

            if ($request->hasFile('fta_document')) {
                $file = $request->file('fta_document');
                $fileName = $file->getClientOriginalName();
                $path = $file->store('uploads/customers/FTA/' . $customer->id, 'public');

                CustomerFtaMedia::create([
                    'customer_id' => $customer->id,
                    'document_name' => $validated["document_name"] ?: $fileName,
                    'file_path' => $path,
                    'start_date' => $validated["start_date"],
                    'expire_date' => $validated["expire_date"],
                ]);

                $fileFullPath = storage_path('app/public/' . $path);
            }

            $customer->status = 3;
            $customer->supervisor_approved_at = now();
            $customer->save();

            $settings = Setting::first();
            $companyEmail = $settings->email ?? 'N/A';
            $companyAddress = $customer->branch ? $customer->branch->branch_name : 'N/A';
            $companyPhone = $settings->phone ?? 'N/A';
            $pdfFilePath = null;

            if ($request->has('send_invoice')) {
                $pdfFilePath = $this->generateInvoicePDF($customer);
            }

            if ($request->has('send_email')) {
                Mail::to($customer->email)->send(new TaxIdAddedMail(
                    $customer,
                    $request->tax_id ?? null,
                    $companyEmail,
                    $companyPhone,
                    $companyAddress,
                    isset($fileFullPath) ? $fileFullPath : null,
                    isset($pdfFilePath) ? $pdfFilePath : null
                ));
            }

            return response()->json(['success' => true, 'message' => __('Media uploaded successfully.')]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error uploading media. ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error uploading media.')
            ], 500);
        }
    }


    public function updateFtaDocument(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date|before:expire_date',
                'expire_date' => 'required|date',
            ], [
                'start_date.before' => 'The start date must be before the expire date.',
                'expire_date.required' => 'The expire date is required.',
            ]);

            CustomerFtaMedia::where('id', $id)->update($validated);

            return response()->json([
                'success' => true,
                'message' => __('FTA document updated successfully.')
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating media. ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Error updating FTA document.')
            ], 500);
        }
    }
    public function updateCreator(Request $request)
    {
        try {
            $customer = Customer::findOrFail($request->id);

            $oldCreator = $customer->created_by;

            $customer->created_by = $request->created_by;
            $customer->save();

            CreatorChangeLog::create([
                'customer_id' => $customer->id,
                'old_creator_id' => $oldCreator,
                'new_creator_id' => $request->created_by,
                'changed_by' => auth()->id(),
            ]);

            return response()->json(['success' => true, 'message' => __('Customer creator updated successfully.')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('Error updating customer creator: ') . $e->getMessage()]);
        }
    }


    public function generateInvoicePDF($customer)
    {
        // $customer = Customer::findOrFail($customer);
        $services = $customer->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->pivot->price,
            ];
        });

        $paymentMethodsReadable = [
            'by_machine' => 'By Machine',
            'by_link' => 'By Link',
            'cashier' => 'Cashier',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer'
        ];

        // Get readable payment method or default to 'N/A'
        $payment_method = $paymentMethodsReadable[$customer->payment_method] ?? 'N/A';
        $companyAddress = $customer->branch ? $customer->branch->branch_name : 'N/A';

        $invoiceData = [
            'customer' => $customer,
            'service' => $services,
            'invoiceNumber' => $customer->invoice_number,
            'receiptNumber' => $customer->serial_number,
            'created' => auth()->user(),
            'date' => now()->format('Y-m-d'),
            'receiptDate' => now()->format('Y-m-d'),
            'vatValue' => $customer->vat_value ?? 0,
            'vatAmount' => ($customer->price ?? 0) * (($customer->vat_value ?? 0) / 100),
            'totalAmount' => ($customer->price ?? 0) + (($customer->price ?? 0) * (($customer->vat_value ?? 0) / 100)),
            'companyEmail' => Setting::first()->email ?? 'N/A',
            'companyAddress' => $companyAddress,
            'companyPhone' => Setting::first()->phone ?? 'N/A',
            'logo' => Setting::first()->website_logo_small ?? 'N/A',
            'payment_method' => $payment_method ?? 'N/A'


        ];

        // ✅ Load the invoice view and generate PDF
        $pdf = PDF::loadView('invoice.invoice-pdf', $invoiceData);

        // ✅ Define PDF directory and file path
        $pdfDirectory = storage_path('app/public/invoices');
        $pdfFilePath = $pdfDirectory . '/invoice-' . $customer->id . '.pdf';

        // ✅ Check if the directory exists, if not, create it
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0777, true); // Create the directory with permissions
        }
        // ✅ Save PDF file to the directory
        Storage::disk('public')->put('invoices/invoice-' . $customer->id . '.pdf', $pdf->output());

        return $pdfFilePath; // ✅ Return the full path of the saved PDF
    }

    public function getProcessTime($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        return response()->json([
            'dataEntry' => $customer->getDataEntryTimeAttribute() ,
            'expertVerification' => $customer->getExpertVerificationTimeAttribute() ,
            'supervisorApproval' => $customer->getSupervisorApprovalTimeAttribute() ,
            'totalVerification' => $customer->getTotalVerificationTimeAttribute() ,
        ]);
    }

    public function editStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3',
            'customer_id' => 'required|exists:customers,id'
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->status = $request->status;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'Customer status updated successfully!']);
    }

    public function accountStatement($id)
    {
        $customer = Customer::findOrFail($id);
        $services = $customer->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->pivot->price,
            ];
        });

        return view('admin.customers.account', compact('services', 'customer'));
    }
}
