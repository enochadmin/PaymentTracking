@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Edit User</h1>
    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div><label for="name">Name</label><input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"></div>
        <div><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"></div>
        <div><label for="password">Password (leave blank to keep)</label><input type="password" id="password" name="password"></div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
            <div class="mt-2 space-y-2">
                @foreach($roles as $role)
                    <div class="flex items-center">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}" {{ $user->roles->contains($role) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded dark:bg-gray-900 dark:border-gray-700 dark:checked:bg-indigo-600">
                        <label for="role-{{ $role->id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100"> {{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <label for="team_id">Team</label>
            <select name="team_id" id="team_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                <option value="">Select a Team</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ old('team_id', $user->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Save</button>
    </form>
</div>
@endsection
