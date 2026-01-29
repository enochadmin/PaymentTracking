@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Create Bank</h1>
    <form method="POST" action="{{ route('banks.store') }}" class="space-y-4">
        @csrf
        <div><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name') }}"></div>
        <div><label for="branch">Branch</label><input type="text" id="branch" name="branch" value="{{ old('branch') }}"></div>
        <div><label for="address">Address</label><textarea id="address" name="address">{{ old('address') }}</textarea></div>
        <div><label for="contact_person">Contact Person</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"></div>
        <div><label for="bank_number">Bank Number</label><input type="text" id="bank_number" name="bank_number" value="{{ old('bank_number') }}"></div>
        <button type="submit">Create</button>
    </form>
</div>
@endsection