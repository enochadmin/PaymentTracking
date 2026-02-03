@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Create Supplier</h1>
    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
        @csrf
        <div><label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label><input type="text" id="name" name="name" value="{{ old('name') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></div>
        <div><label for="tin_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tin Number</label><input type="text" id="tin_number" name="tin_number" value="{{ old('tin_number') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100"></div>
        <div><label for="contact_person">Contact person</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"></div>
        <div><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}"></div>
        <div><label for="phone">Phone</label><input type="text" id="phone" name="phone" value="{{ old('phone') }}"></div>
        <div><label for="address">Address</label><textarea id="address" name="address">{{ old('address') }}</textarea></div>
        <div>
            <label for="supplier_type">Supplier Type</label>
            <select name="supplier_type" id="supplier_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                @foreach(['Supply only', 'Subcontractor', 'Consultant'] as $typeOption)
                    <option value="{{ $typeOption }}" {{ old('supplier_type') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Create</button>
    </form>
</div>
@endsection
