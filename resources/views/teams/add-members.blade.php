<div class="mt-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add Member</h3>
    @if($availableUsers->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">No available users to add.</p>
    @else
        <form action="{{ route('teams.members.add', $team) }}" method="POST" class="flex items-end gap-4">
            @csrf
            <div class="w-full max-w-xs">
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select User</label>
                <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    @foreach($availableUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add Member
            </button>
        </form>
        @error('user_id')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif
</div>
