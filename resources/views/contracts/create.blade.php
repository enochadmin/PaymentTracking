@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
        {{ isset($paymentRequest) ? 'Create Contract from Payment Request' : 'Create New Contract' }}
    </h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Payment Request -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Payment Document</label>
                @if(isset($paymentDocument))
                    <input type="hidden" name="payment_document_id" value="{{ $paymentDocument->id }}">
                    <p class="text-gray-900 dark:text-gray-100">{{ $paymentDocument->reason_for_payment }}</p>
                @else
                    <select name="payment_document_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select Payment Document</option>
                        {{-- 
                             In a real scenario, we might want to pass available documents to the view if creating from scratch.
                             For now, this path might be less used or we need to pass $paymentDocuments from controller.
                        --}}
                    </select>
                @endif
            </div>

            <!-- Supplier -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Supplier</label>
                @if(isset($paymentDocument))
                    <input type="hidden" name="supplier_id" value="{{ $paymentDocument->supplier_id }}">
                    <p class="text-gray-900 dark:text-gray-100">{{ $paymentDocument->supplier->name }} ({{ $paymentDocument->supplier->supplier_type }})</p>
                @else
                    <select name="supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->supplier_type }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Project -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Project</label>
                @if(isset($paymentDocument))
                    <input type="hidden" name="project_id" value="{{ $paymentDocument->project_id }}">
                    <p class="text-gray-900 dark:text-gray-100">{{ $paymentDocument->project->name }}</p>
                @else
                    <select name="project_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Contract Number -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_number">
                    Contract Number *
                </label>
                <input type="text" name="contract_number" id="contract_number" value="{{ old('contract_number') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Title -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_title">
                    Contract Title *
                </label>
                <input type="text" name="contract_title" id="contract_title" value="{{ old('contract_title', isset($paymentDocument) ? $paymentDocument->reason_for_payment : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Type -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_type">
                    Contract Type *
                </label>
                <select name="contract_type" id="contract_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Type</option>
                    <option value="Subcontractor" {{ old('contract_type', isset($paymentDocument) && $paymentDocument->supplier->supplier_type === 'Subcontractor' ? 'Subcontractor' : '') === 'Subcontractor' ? 'selected' : '' }}>Subcontractor</option>
                    <option value="Consultant" {{ old('contract_type', isset($paymentDocument) && $paymentDocument->supplier->supplier_type === 'Consultant' ? 'Consultant' : '') === 'Consultant' ? 'selected' : '' }}>Consultant</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="start_date">
                    Start Date *
                </label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- End Date -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="end_date">
                    End Date *
                </label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Value -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_value">
                    Contract Value *
                </label>
                <input type="number" step="0.01" name="contract_value" id="contract_value" value="{{ old('contract_value', isset($paymentDocument) ? $paymentDocument->amount : '') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Currency -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="currency">
                    Currency *
                </label>
                <input type="text" name="currency" id="currency" value="{{ old('currency', isset($paymentDocument) ? $paymentDocument->currency : 'USD') }}" maxlength="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="status">
                    Status *
                </label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="terminated">Terminated</option>
                </select>
            </div>

            <!-- Contract Document -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_document">
                    Contract Document
                </label>
                <input type="file" name="contract_document" id="contract_document" accept=".pdf,.doc,.docx" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>

        <!-- Payment Terms -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="payment_terms">
                Payment Terms
            </label>
            <textarea name="payment_terms" id="payment_terms" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('payment_terms') }}</textarea>
        </div>

        <!-- Scope of Work -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="scope_of_work">
                Scope of Work
            </label>
            <textarea name="scope_of_work" id="scope_of_work" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('scope_of_work') }}</textarea>
        </div>

        <!-- Deliverables -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="deliverables">
                Deliverables
            </label>
            <textarea name="deliverables" id="deliverables" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deliverables') }}</textarea>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="notes">
                Notes
            </label>
            <textarea name="notes" id="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('contracts.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Contract
            </button>
        </div>
    </form>
</div>
@endsection
