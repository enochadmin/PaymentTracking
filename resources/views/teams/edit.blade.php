@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Edit Team</h1>
        <a href="{{ route('teams.members.form', $team) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add Members
        </a>
    </div>
    <form method="POST" action="{{ route('teams.update', $team) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name', $team->name) }}"></div>
        <button type="submit">Update</button>
    </form>
</div>
@endsection