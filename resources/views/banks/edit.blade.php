@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Edit Bank</h1>
    <form method="POST" action="{{ route('banks.update', $bank) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name', $bank->name) }}"></div>
        <div><label for="branch">Branch</label><input type="text" id="branch" name="branch" value="{{ old('branch', $bank->branch) }}"></div>
        <div><label for="address">Address</label><textarea id="address" name="address">{{ old('address', $bank->address) }}</textarea></div>
        <div><label for="contact_person">Contact Person</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $bank->contact_person) }}"></div>
        <div><label for="bank_number">Bank Number</label><input type="text" id="bank_number" name="bank_number" value="{{ old('bank_number', $bank->bank_number) }}"></div>
        <button type="submit">Update</button>
    </form>
</div>
@endsection