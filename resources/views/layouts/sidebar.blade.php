<aside class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4 h-full min-h-screen lg:rounded-md">
    <nav class="space-y-2">
        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-6">Management</div>

        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-nav-link>

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('users.access'))
            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                {{ __('Users') }}
            </x-nav-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('suppliers.access'))
            <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                {{ __('Suppliers') }}
            </x-nav-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('projects.access'))
            <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                {{ __('Projects') }}
            </x-nav-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('banks.access'))
            <x-nav-link :href="route('banks.index')" :active="request()->routeIs('banks.*')">
                {{ __('Banks') }}
            </x-nav-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('teams.access'))
            <x-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.*')">
                {{ __('Teams') }}
            </x-nav-link>
        @endif
    </nav>
</aside>
