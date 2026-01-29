@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $supplier->name }}</h1>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Contact Person: {{ $supplier->contact_person }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Email: {{ $supplier->email }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Phone: {{ $supplier->phone }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Supplier Type: {{ $supplier->supplier_type }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-6">Address: {{ $supplier->address }}</p>
    <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-600">Edit</a>
</div>
@endsection
