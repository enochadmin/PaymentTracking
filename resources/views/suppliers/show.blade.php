@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $supplier->name }}</h1>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Contact Person: {{ $supplier->contact_person }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Email: {{ $supplier->email }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Phone: {{ $supplier->phone }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Supplier Type: {{ $supplier->supplier_type }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-6">Address: {{ $supplier->address }}</p>
    
    @if($supplier->business_license_path)
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Documents</h3>
            <a href="{{ asset('storage/' . $supplier->business_license_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Business License
            </a>
        </div>
    @endif
    <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-600">Edit</a>
</div>
@endsection
