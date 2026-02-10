@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit Contract</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contracts.update', $contract) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Supplier -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Supplier</label>
                <select name="supplier_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $contract->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }} ({{ $supplier->supplier_type }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Project -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Project</label>
                <select name="project_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $contract->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Contract Number -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_number">
                    Contract Number *
                </label>
                <input type="text" name="contract_number" id="contract_number" value="{{ old('contract_number', $contract->contract_number) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Title -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_title">
                    Contract Title *
                </label>
                <input type="text" name="contract_title" id="contract_title" value="{{ old('contract_title', $contract->contract_title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Type -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_type">
                    Contract Type *
                </label>
                <select name="contract_type" id="contract_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="Subcontractor" {{ old('contract_type', $contract->contract_type) === 'Subcontractor' ? 'selected' : '' }}>Subcontractor</option>
                    <option value="Consultant" {{ old('contract_type', $contract->contract_type) === 'Consultant' ? 'selected' : '' }}>Consultant</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="start_date">
                    Start Date *
                </label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- End Date -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="end_date">
                    End Date *
                </label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Contract Value -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_value">
                    Contract Value *
                </label>
                <input type="number" step="0.01" name="contract_value" id="contract_value" value="{{ old('contract_value', $contract->contract_value) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Currency -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="currency">
                    Currency *
                </label>
                <input type="text" name="currency" id="currency" value="{{ old('currency', $contract->currency) }}" maxlength="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="status">
                    Status *
                </label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="draft" {{ old('status', $contract->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', $contract->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ old('status', $contract->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="terminated" {{ old('status', $contract->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <!-- Contract Document -->
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="contract_document">
                    Contract Document
                </label>
                <input type="file" name="contract_document" id="contract_document" accept=".pdf,.doc,.docx" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @if($contract->contract_document_path)
                    <p class="text-xs text-gray-500 mt-1">Current: <a href="{{ asset('storage/' . $contract->contract_document_path) }}" target="_blank" class="text-indigo-600">View Document</a></p>
                @endif
            </div>
        </div>

        <!-- Payment Terms -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="payment_terms">
                Payment Terms
            </label>
            <textarea name="payment_terms" id="payment_terms" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('payment_terms', $contract->payment_terms) }}</textarea>
        </div>

        <!-- Scope of Work -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="scope_of_work">
                Scope of Work
            </label>
            <textarea name="scope_of_work" id="scope_of_work" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('scope_of_work', $contract->scope_of_work) }}</textarea>
        </div>

        <!-- Deliverables -->
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="deliverables">
                Deliverables
            </label>
            <textarea name="deliverables" id="deliverables" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deliverables', $contract->deliverables) }}</textarea>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="notes">
                Notes
            </label>
            <textarea name="notes" id="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes', $contract->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('contracts.show', $contract) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Contract
            </button>
        </div>
    </form>
</div>
@endsection
