@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('final-payment-requests.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 block mb-2">
                &larr; Back to Requests
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Final Payment Request #{{ $finalPaymentRequest->id }}
            </h1>
        </div>
        <div>
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full 
                @if($finalPaymentRequest->status === 'paid') bg-green-100 text-green-800
                @elseif($finalPaymentRequest->status === 'finance_approved') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                {{ ucfirst(str_replace('_', ' ', $finalPaymentRequest->status)) }}
            </span>
        </div>
    </div>

    <!-- Finance Approval Section -->
    @if(Auth::user()->can('final_payment_requests.approve_finance') && $finalPaymentRequest->status === 'submitted_to_finance')
    <div class="bg-indigo-50 dark:bg-gray-700 shadow sm:rounded-lg mb-6 p-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-100 mb-2">Finance Actions</h3>
        <form action="{{ route('final-payment-requests.approveFinance', $finalPaymentRequest) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="finance_comments" class="block text-sm font-medium text-indigo-800 dark:text-indigo-200">Comments</label>
                <textarea name="finance_comments" id="finance_comments" rows="2" class="mt-1 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out" onclick="return confirm('Approve this payment?')">
                Approve Payment
            </button>
        </form>
    </div>
    @endif

    <!-- Payment Processing Section -->
    @if(Auth::user()->can('final_payment_requests.mark_paid') && $finalPaymentRequest->status === 'finance_approved')
    <div class="bg-green-50 dark:bg-gray-700 shadow sm:rounded-lg mb-6 p-6 border-l-4 border-green-500">
        <h3 class="text-lg font-medium text-green-900 dark:text-green-100 mb-2">Process Payment</h3>
        <form action="{{ route('final-payment-requests.markPaid', $finalPaymentRequest) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-2 mb-4">
                <div>
                    <label for="cheque_number" class="block text-sm font-medium text-green-800 dark:text-green-200">Cheque/Trx Number</label>
                    <input type="text" name="cheque_number" class="mt-1 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md" required>
                </div>
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-green-800 dark:text-green-200">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="mt-1 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md" required>
                </div>
            </div>
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-6 rounded shadow transition duration-150 ease-in-out" onclick="return confirm('Mark as PAID?')">
                Mark as Paid
            </button>
        </form>
    </div>
    @endif

    <!-- Details Card -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Request Information</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Project</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $finalPaymentRequest->project->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $finalPaymentRequest->supplier->name }}</dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested Amount</dt>
                <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">{{ $finalPaymentRequest->currency }} {{ number_format($finalPaymentRequest->amount, 2) }}</dd>
            </div>
            <div>
                 <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date Submitted</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $finalPaymentRequest->created_at->format('Y-m-d H:i') }}</dd>
            </div>

            <div class="col-span-1 md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $finalPaymentRequest->notes ?? 'N/A' }}</dd>
            </div>
        </div>
    </div>
    
    <!-- Linked Info -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Related Documents</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 space-y-4">
             <div>
                <h4 class="text-sm font-bold">Original Payment Document</h4>
                <a href="{{ route('payment-documents.show', $finalPaymentRequest->paymentDocument) }}" class="text-indigo-600 hover:underline">View Document #{{ $finalPaymentRequest->paymentDocument->id }}</a>
             </div>
             
             @if($finalPaymentRequest->contract)
             <div>
                <h4 class="text-sm font-bold">Contract</h4>
                <a href="{{ route('contracts.show', $finalPaymentRequest->contract) }}" class="text-indigo-600 hover:underline">View Contract {{ $finalPaymentRequest->contract->contract_number }}</a>
             </div>
             @endif
        </div>
    </div>
</div>
@endsection
