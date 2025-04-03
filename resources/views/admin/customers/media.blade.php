@extends('admin.layouts.master')

@section('page_title')
    {{ __('Upload Media for ') . $customer->first_name . ' ' . $customer->last_name }}
@endsection
<style>
    .modal-lg {
        max-width: 90%;
        /* Increase modal width to 90% of the viewport */
    }

    .modal-body {
        max-height: 70vh;
        /* Limit modal body height to 70% of the viewport height */
        overflow-y: auto;
        /* Enable scrolling for large content */
    }
</style>
@section('content')
    <div class="page-header">
        <div class="card breadcrumb-card">
            <div class="row justify-content-between align-content-between" style="height: 100%;">
                <div class="col-md-6">
                    <h3 class="page-title">{{ __('Upload Media') }}</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('customers.index') }}">Customers</a>
                        </li>
                        <li class="breadcrumb-item active-breadcrumb">
                            {{ __('Upload Media for ') . $customer->first_name . ' ' . $customer->last_name }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ __('Upload Media') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($customer->status < 2)
                                <form action="{{ route('customers.media.store', $customer->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="document_type" class="required">
                                            {{ __('Document Type') }}
                                        </label>
                                        <select name="document_name" id="document_type" class="form-control @error('document_type') is-invalid @enderror" required>
                                            <option value="" disabled selected>{{ __('Select Document Type') }}</option>
                                            @foreach (\App\Enums\DocumentType::all() as $value => $label)
                                                <option value="{{ $value }}" {{ old('document_name') === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        @error('document_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    
                                    <div class="form-group">
                                        <label for="media" class="required">
                                            {{ __('Select Media') }}
                                        </label>
                                        <input type="file" name="media[]" id="media"
                                            class="form-control @error('media') is-invalid @enderror" multiple required>
                                        @error('media')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">{{ __('Upload') }}</button>
                                </form>
                                
                            @else
                                <div class="alert alert-danger text-center">
                                    <i class="fe fe-lock"></i>
                                    {{ __('Media uploads are not allowed as this customer is currently in process.') }}
                                </div>
                                <div class="text-center mt-4">
                                    <div class="text-muted">
                                        <i class="fe fe-lock" style="font-size: 2rem; color: #6c757d;"></i>
                                    </div>
                                    <p class="text-muted mt-2" style="font-size: 1.1rem; font-weight: 500;">
                                        {{ __('This customer has been submitted for verification.') }}
                                    </p>
                                    <p class="text-muted" style="font-size: 0.95rem;">
                                        {{ __('For now, please contact the administrator or wait for further updates.') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card shadow mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ __('Verification Status') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            @if (empty($customer->transaction_refrence_number))
                                <div class="mb-3">
                                    <span class="badge badge-danger" style="font-size: 1rem;">
                                        <i class="fe fe-x-circle"></i> {{ __('Not Paid') }}
                                    </span>
                                </div>
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">
                                        <i class="fe fe-alert-circle"></i> {{ __('Payment Required') }}
                                    </h6>
                                    <p class="mb-0">
                                        {{ __('This customer has not made a payment yet. Please complete the payment process.') }}
                                    </p>
                                </div>
                            @else
                                @if ($customer->status == 0)
                                    <form action="{{ route('customers.submit.verification', $customer->id) }}" method="POST">
                                        @csrf
                                        <p class="mb-3 text-muted">
                                            <i class="fe fe-alert-circle text-warning"></i>
                                            {{ __('To proceed, please submit this customer for verification.') }}
                                        </p>
                                        <button type="submit" class="btn btn-warning"
                                            {{ $customer->media->count() > 0 ? '' : 'disabled' }}>
                                            <i class="fe fe-send"></i> {{ __('Submit') }}
                                        </button>
                                    </form>
                                @elseif ($customer->status == 1)
                                    <div class="mb-3">
                                        <span class="badge badge-warning" style="font-size: 1rem;">
                                            <i class="fe fe-clock"></i> {{ __('In Progress') }}
                                        </span>
                                    </div>
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">
                                            <i class="fe fe-info"></i> {{ __('In Progress') }}
                                        </h6>
                                        <p class="mb-0">
                                            {{ __('This customer is currently in progress and awaiting verification.') }}
                                        </p>
                                    </div>
                                @elseif ($customer->status == 2)
                                    <div class="mb-3">
                                        <span class="badge badge-success" style="font-size: 1rem;">
                                            <i class="fe fe-check-circle"></i> {{ __('Verified') }}
                                        </span>
                                    </div>
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">
                                            <i class="fe fe-check"></i> {{ __('Verified') }}
                                        </h6>
                                        <p class="mb-0">
                                            {{ __('This customer has been successfully verified.') }}
                                        </p>
                                    </div>
                                @elseif ($customer->status == 3)
                                    <div class="mb-3">
                                        <span class="badge badge-success" style="font-size: 1rem;">
                                            <i class="fe fe-check-circle"></i> {{ __('Process Completed') }}
                                        </span>
                                    </div>
                                    <div class="alert alert-success">
                                        <h6 class="alert-heading">
                                            <i class="fe fe-thumbs-up"></i> {{ __('Approved') }}
                                        </h6>
                                        <p class="mb-0">
                                            {{ __('The process for this customer has been successfully completed and the customer has been approved.') }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Uploaded Documents') }}</h5>
                </div>
                <div class="card-body">
                    @if ($customer->media->count())
                        <table class="table table-bordered align-middle text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Document Name') }}</th>
                                    <th>{{ __('Preview / View') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->media as $media)
                                    <tr>
                                        <td class="font-weight-bold">{{ ucwords(str_replace('_', ' ', $media->document_name)) }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-file"
                                                data-path="{{ asset('storage/' . $media->file_path) }}"
                                                data-type="{{ pathinfo($media->file_path, PATHINFO_EXTENSION) }}">
                                                <i class="fe fe-eye"></i> {{ __('View') }}
                                            </button>
                                        </td>
                                        @if (Gate::check('customers-delete-media'))
                                            @if (auth()->user()->hasRole('operator') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') )
                                                <td>
                                                    <form action="{{ route('customers.media.delete', $media->id) }}"
                                                        method="POST" class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fe fe-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            @else
                                                <td class="text-muted">
                                                    <i class="fe fe-lock"></i>
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <div class="text-muted">
                                <i class="fe fe-folder" style="font-size: 2rem; color: #6c757d;"></i>
                            </div>
                            <p class="text-muted mt-2" style="font-size: 1.1rem; font-weight: 500;">
                                {{ __('No documents uploaded yet.') }}
                            </p>
                            <p class="text-muted" style="font-size: 0.95rem;">
                                {{ __('Upload documents to manage and preview them here.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div>

    <!-- File Viewer Modal -->
    <div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileViewerModalLabel">{{ __('File Viewer') }}</h5>

                    <i class="btn-close" style="color:#c4ab54 !important; pointer:cursor" data-feather="x-circle"></i>

                </div>
                <div class="modal-body text-center" id="fileViewerContent">
                    <!-- File content will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('fileViewerModal'));

            // Attach click event to view buttons
            document.querySelectorAll('.view-file').forEach(function(button) {
                button.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-path');
                    const fileType = this.getAttribute('data-type');
                    const modalContent = document.getElementById('fileViewerContent');

                    // Clear previous content
                    modalContent.innerHTML = '';

                    // Load appropriate viewer
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType.toLowerCase())) {
                        modalContent.innerHTML =
                            `<img src="${filePath}" alt="File Preview" class="img-fluid">`;
                    } else if (fileType.toLowerCase() === 'pdf') {
                        modalContent.innerHTML =
                            `<embed src="${filePath}" width="100%" height="600px" type="application/pdf">`;
                    } else {
                        modalContent.innerHTML =
                            `<a href="${filePath}" target="_blank" class="btn btn-primary">{{ __('Download File') }}</a>`;
                    }

                    // Show modal
                    modal.show();
                });
            });

            // Ensure modal is hidden when the close button is clicked
            document.querySelector('.btn-close').addEventListener('click', function() {
                modal.hide();
            });
        });
    </script>
@endpush
