@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('procurement-review.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 block mb-2">
                &larr; Back to Queue
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Review Payment Document #{{ $paymentDocument->id }}
            </h1>
        </div>
    </div>

    <!-- Reviewer Actions -->
    <div class="bg-indigo-50 dark:bg-gray-700 shadow sm:rounded-lg mb-6 p-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-2">Reviewer Actions</h3>
        <p class="text-sm text-indigo-700 dark:text-indigo-200 mb-4">
            Review the document details below. Only approve if all information is correct and documents are valid.
        </p>
        <div class="flex space-x-4">
            <form action="{{ route('procurement-review.approve', $paymentDocument) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out" onclick="return confirm('Are you sure you want to approve this document?')">
                    Approve Document
                </button>
            </form>
            
            <form action="{{ route('procurement-review.reject', $paymentDocument) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out" onclick="return confirm('Are you sure you want to REJECT this document?')">
                    Reject Document
                </button>
            </form>
        </div>
    </div>

    <!-- Details Card (Reused structure or component) -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Document Information</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Same details as show view -->
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Project</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paymentDocument->project->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paymentDocument->supplier->name }} ({{ $paymentDocument->supplier->supplier_type }})</dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paymentDocument->user->name }}</dd>
            </div>
            <div>
                 <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submission Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paymentDocument->submission_date->format('Y-m-d') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount</dt>
                <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $paymentDocument->currency }} {{ number_format($paymentDocument->amount, 2) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Type</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $paymentDocument->payment_type }}</dd>
            </div>

            <div class="col-span-1 md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reason for Payment</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{ $paymentDocument->reason_for_payment }}</dd>
            </div>
        </div>
    </div>

    <!-- Files -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Attachments for Review</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Invoice</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    @if($paymentDocument->invoice_path)
                        <a href="{{ Storage::url($paymentDocument->invoice_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Download/View Invoice
                        </a>
                    @else
                        <span class="text-red-500">Missing Invoice</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Note</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    @if($paymentDocument->delivery_note_path)
                        <a href="{{ Storage::url($paymentDocument->delivery_note_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                            Download/View Delivery Note
                        </a>
                    @else
                        <span class="text-gray-500">Not provided</span>
                    @endif
                </dd>
            </div>
        </div>
    </div>
</div>
@endsection
