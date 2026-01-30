@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('payment-documents.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">
            &larr; Back to Listings
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Edit Payment Document #{{ $paymentDocument->id }}
            </h3>
        </div>
        
        <form action="{{ route('payment-documents.update', $paymentDocument) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project</label>
                    <select id="project_id" name="project_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $paymentDocument->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select a Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $paymentDocument->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} ({{ $supplier->supplier_type }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Details -->
                <div class="sm:col-span-2">
                    <label for="reason_for_payment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Payment</label>
                    <textarea id="reason_for_payment" name="reason_for_payment" rows="3" class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('reason_for_payment', $paymentDocument->reason_for_payment) }}</textarea>
                </div>

                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Type</label>
                    <select id="payment_type" name="payment_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select Type</option>
                        <option value="Advance" {{ old('payment_type', $paymentDocument->payment_type) == 'Advance' ? 'selected' : '' }}>Advance</option>
                        <option value="Partial" {{ old('payment_type', $paymentDocument->payment_type) == 'Partial' ? 'selected' : '' }}>Partial</option>
                        <option value="Final" {{ old('payment_type', $paymentDocument->payment_type) == 'Final' ? 'selected' : '' }}>Final</option>
                    </select>
                </div>

                <div>
                    <label for="cost_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cost Type</label>
                    <select id="cost_type" name="cost_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        <option value="">Select Cost Type</option>
                        <option value="Direct" {{ old('cost_type', $paymentDocument->cost_type) == 'Direct' ? 'selected' : '' }}>Direct</option>
                        <option value="Indirect" {{ old('cost_type', $paymentDocument->cost_type) == 'Indirect' ? 'selected' : '' }}>Indirect</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="amount" id="amount" step="0.01" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0.00" value="{{ old('amount', $paymentDocument->amount) }}" required>
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <select id="currency" name="currency" class="focus:ring-indigo-500 focus:border-indigo-500 h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
                                <option>USD</option>
                                <option>EUR</option>
                                <option>GBP</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="responsible_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsible Person</label>
                    <input type="text" name="responsible_person" id="responsible_person" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ old('responsible_person', $paymentDocument->responsible_person) }}" required>
                </div>

                <div>
                    <label for="received_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Received Date</label>
                    <input type="date" name="received_date" id="received_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ old('received_date', $paymentDocument->received_date->format('Y-m-d')) }}" required>
                </div>

                <div>
                    <label for="submission_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submission Date</label>
                    <input type="date" name="submission_date" id="submission_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ old('submission_date', $paymentDocument->submission_date->format('Y-m-d')) }}" required>
                </div>

                <!-- Files -->
                <div class="sm:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Attachments</h4>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="invoice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice File (PDF/Image)</label>
                            @if($paymentDocument->invoice_path)
                                <div class="mb-2 text-sm text-gray-600">Current: <a href="{{ Storage::url($paymentDocument->invoice_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">View File</a></div>
                            @endif
                            <input type="file" name="invoice" id="invoice" class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                        </div>

                        <div>
                            <label for="delivery_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Note (PDF/Image)</label>
                            @if($paymentDocument->delivery_note_path)
                                <div class="mb-2 text-sm text-gray-600">Current: <a href="{{ Storage::url($paymentDocument->delivery_note_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">View File</a></div>
                            @endif
                            <input type="file" name="delivery_note" id="delivery_note" class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="submit" name="submit_action" value="draft" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                    Save as Draft
                </button>
                <button type="submit" name="submit_action" value="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-md transition duration-150 ease-in-out">
                    Submit for Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
