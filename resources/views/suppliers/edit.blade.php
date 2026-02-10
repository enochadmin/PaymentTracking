@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Edit Supplier</h1>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div><label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label><input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></div>
        <div><label for="tin_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tin Number</label><input type="text" id="tin_number" name="tin_number" value="{{ old('tin_number', $supplier->tin_number) }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></div>
        <div><label for="contact_person">Contact person</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"></div>
        <div><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}"></div>
        <div><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"></div>
        <div><label for="address">Address</label><textarea id="address" name="address">{{ old('address', $supplier->address) }}</textarea></div>
        <div>
            <label for="supplier_type">Supplier Type</label>
            <select name="supplier_type" id="supplier_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                @foreach(['Supply only', 'Subcontractor', 'Consultant'] as $typeOption)
                    <option value="{{ $typeOption }}" {{ old('supplier_type', $supplier->supplier_type) == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="business_license" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Business License (Optional)</label>
            <input type="file" id="business_license" name="business_license" accept=".pdf,.jpg,.jpeg,.png" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-800 dark:file:text-gray-200">
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted formats: PDF, JPG, PNG. Max size: 10MB.</p>
            @if($supplier->business_license_path)
                <div class="mt-2">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Current File:</p>
                    <a href="{{ asset('storage/' . $supplier->business_license_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 underline text-sm flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        View Business License
                    </a>
                </div>
            @endif
        </div>
        <button type="submit">Update</button>
    </form>
</div>
@endsection