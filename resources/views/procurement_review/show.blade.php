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
    <div class="bg-indigo-50 dark:bg-gray-700 shadow sm:rounded-lg mb-6 p-6 border-l-4 border-indigo-500" x-data="{ approveModal: false, rejectModal: false, rejectionReason: '', reviewNotes: '', reviewDate: '' }">
        <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-2">Reviewer Actions</h3>
        <p class="text-sm text-indigo-700 dark:text-indigo-200 mb-4">
            Review the document details below. Only approve if all information is correct and documents are valid.
        </p>
        <div class="flex space-x-4">
            <button @click="approveModal = true" type="button" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                Approve Document
            </button>
            
            <button @click="rejectModal = true" type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                Reject Document
            </button>
        </div>

        <!-- Approve Modal -->
        <div x-show="approveModal" 
             x-cloak
             @click.away="approveModal = false"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('procurement-review.approve', $paymentDocument) }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        Approve Payment Document
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            You are about to approve this payment document. You can optionally add review notes.
                                        </p>
                                        
                                        <div>
                                            <label for="approve_review_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Review Notes (Optional)
                                            </label>
                                            <textarea 
                                                id="approve_review_notes" 
                                                name="review_notes" 
                                                rows="3" 
                                                x-model="reviewNotes"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Add any notes about this approval..."></textarea>
                                        </div>

                                        <div>
                                            <label for="approve_review_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Review Date (Optional)
                                            </label>
                                            <input 
                                                type="date" 
                                                id="approve_review_date" 
                                                name="review_date" 
                                                x-model="reviewDate"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to use today's date</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Confirm Approval
                            </button>
                            <button @click="approveModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div x-show="rejectModal" 
             x-cloak
             @click.away="rejectModal = false"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('procurement-review.reject', $paymentDocument) }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        Reject Payment Document
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Please provide a reason for rejecting this document. This will be visible to the procurement officer.
                                        </p>
                                        
                                        <div>
                                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Rejection Reason <span class="text-red-500">*</span>
                                            </label>
                                            <textarea 
                                                id="rejection_reason" 
                                                name="rejection_reason" 
                                                rows="4" 
                                                required
                                                x-model="rejectionReason"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                                placeholder="Explain why this document is being rejected..."></textarea>
                                            @error('rejection_reason')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="reject_review_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Additional Notes (Optional)
                                            </label>
                                            <textarea 
                                                id="reject_review_notes" 
                                                name="review_notes" 
                                                rows="2" 
                                                x-model="reviewNotes"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Any additional notes..."></textarea>
                                        </div>

                                        <div>
                                            <label for="reject_review_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Review Date (Optional)
                                            </label>
                                            <input 
                                                type="date" 
                                                id="reject_review_date" 
                                                name="review_date" 
                                                x-model="reviewDate"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to use today's date</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit" 
                                :disabled="!rejectionReason.trim()"
                                :class="rejectionReason.trim() ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Confirm Rejection
                            </button>
                            <button @click="rejectModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Document Information</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
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

            @if($paymentDocument->review_deadline)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Review Deadline</dt>
                <dd class="mt-1 text-sm">
                    @php
                        $status = $paymentDocument->deadline_status;
                        $colors = [
                            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                            'near' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                            'on_track' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100',
                        ];
                        $labels = [
                            'expired' => 'Expired',
                            'near' => 'Near Deadline',
                            'on_track' => 'On Track',
                        ];
                    @endphp
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colors[$status] ?? '' }}">
                        {{ $paymentDocument->review_deadline->format('M d, Y') }} - {{ $labels[$status] ?? '' }}
                    </span>
                </dd>
            </div>
            @endif

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

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
