@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboards.commercial') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
            &larr; Back to Dashboard
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Create Final Payment Request
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                Submitting payment request to Finance for Project: {{ $paymentDocument->project->name }}
            </p>
        </div>
        
        <form action="{{ route('final-payment-requests.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="payment_document_id" value="{{ $paymentDocument->id }}">
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                <!-- Read-only Info -->
                <div class="sm:col-span-2 bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Original Request Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500 block">Supplier</span>
                            <span class="text-sm font-medium">{{ $paymentDocument->supplier->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Amount</span>
                            <span class="text-sm font-medium">{{ $paymentDocument->currency }} {{ number_format($paymentDocument->amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Payment Type</span>
                            <span class="text-sm font-medium">{{ $paymentDocument->payment_type }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block">Reviewer</span>
                            <span class="text-sm font-medium">{{ $paymentDocument->reviewer->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contract Linking -->
                <div class="sm:col-span-2">
                    <label for="contract_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Associated Contract</label>
                    <div class="mt-1">
                        @if($paymentDocument->contract)
                            <input type="text" disabled value="{{ $paymentDocument->contract->contract_number }} - {{ $paymentDocument->contract->contract_title }}" 
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-100 text-gray-600 cursor-not-allowed">
                            <input type="hidden" name="contract_id" value="{{ $paymentDocument->contract->id }}">
                            <p class="mt-1 text-sm text-green-600">Locked to contract created from this document.</p>
                        @elseif($paymentDocument->supplier->supplier_type == 'Supply only')
                             <p class="text-sm text-gray-500 italic">No contract required for Supply Only.</p>
                             <input type="hidden" name="contract_id" value="">
                        @else
                             <p class="text-sm text-yellow-600">No contract linked. Are you sure? (Usually required for Subcontractor/Consultant)</p>
                        @endif
                    </div>
                </div>

                <!-- Fields Confirmation -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount to Pay</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount', $paymentDocument->amount) }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                         <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">{{ $paymentDocument->currency }}</span>
                            <input type="hidden" name="currency" value="{{ $paymentDocument->currency }}">
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Confirm final amount.</p>
                </div>

                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Type</label>
                    <input type="text" name="payment_type" value="{{ old('payment_type', $paymentDocument->payment_type) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Notes for Finance</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Any specific instructions...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-md transition duration-150 ease-in-out">
                    Submit to Finance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
