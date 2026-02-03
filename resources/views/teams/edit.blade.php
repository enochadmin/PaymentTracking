@extends('layouts.app')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Settings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Team Settings</h3>
            <form method="POST" action="{{ route('teams.update', $team) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $team->name) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Manager</label>
                    <select name="manager_id" id="manager_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                        <option value="">Select a Manager</option>
                        @foreach($availableManagers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id', $team->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Team Members -->
        <div class="space-y-6">
            <!-- Add Member -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Member</h3>
                 <form method="POST" action="{{ route('teams.members.add', $team) }}" class="flex gap-2">
                    @csrf
                    <div class="flex-grow">
                        <select name="user_id" class="block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Select User to Add</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Add
                    </button>
                </form>
            </div>

            <!-- Current Members List -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                 <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Current Members</h3>
                 @if($team->users->isEmpty())
                    <p class="text-gray-500 text-sm">No members in this team.</p>
                 @else
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($team->users as $user)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                @if($team->manager_id !== $user->id)
                                    <form method="POST" action="{{ route('teams.members.remove', [$team, $user]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium" onclick="return confirm('Remove this user from the team?')">Remove</button>
                                    </form>
                                @else
                                    <span class="text-xs text-indigo-600 font-semibold border border-indigo-200 rounded px-2 py-1 bg-indigo-50 dark:bg-indigo-900 dark:border-indigo-700 dark:text-indigo-300">Manager</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                 @endif
            </div>
        </div>
    </div>
</div>
@endsection