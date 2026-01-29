@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $bank->name }}</h1>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Branch: {{ $bank->branch }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Address: {{ $bank->address }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-2">Bank Number: {{ $bank->bank_number }}</p>
    <p class="text-gray-700 dark:text-gray-300 mb-6">Contact Person: {{ $bank->contact_person }}</p>
    <a href="{{ route('banks.edit', $bank) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-700 dark:hover:bg-indigo-600">Edit</a>
</div>
@endsection