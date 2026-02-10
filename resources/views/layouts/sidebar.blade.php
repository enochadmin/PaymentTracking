<aside class="bg-gradient-to-b from-teal-800 to-teal-950 dark:from-gray-900 dark:to-gray-950 border-r border-teal-700/30 dark:border-gray-800/60 
              backdrop-blur-sm bg-opacity-90 dark:bg-opacity-80 shadow-xl 
              p-5 lg:rounded-r-xl text-white h-full min-h-screen overflow-y-auto transition-all duration-300">
    
    <nav class="space-y-1.5">
        <div class="text-xs font-bold uppercase tracking-wider text-teal-200/80 dark:text-gray-400 mb-6 px-3">
            Management
        </div>

        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </x-slot>
            <span class="text-sm font-medium">{{ __('Dashboard') }}</span>
        </x-sidebar-link>

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('users.access'))
            <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Users') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('suppliers.access'))
            <x-sidebar-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Suppliers') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('projects.access'))
            <x-sidebar-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Projects') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('banks.access'))
            <x-sidebar-link :href="route('banks.index')" :active="request()->routeIs('banks.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Banks') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('teams.access'))
            <x-sidebar-link :href="route('teams.index')" :active="request()->routeIs('teams.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">
                    {{ Auth::user()->hasRole('admin') ? __('Teams') : __('My Team') }}
                </span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('procurement'))
            <x-sidebar-link :href="route('payment-documents.index')" :active="request()->routeIs('payment-documents.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Payment Documents') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('procurement_reviewer'))
            <x-sidebar-link :href="route('procurement-review.index')" :active="request()->routeIs('procurement-review.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Review Queue') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('commercial'))
            <x-sidebar-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Contracts') }}</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('commercial.approved-documents.index')" :active="request()->routeIs('commercial.approved-documents.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Approved Documents') }}</span>
            </x-sidebar-link>

            <x-sidebar-link :href="route('final-payment-requests.index')" :active="request()->routeIs('final-payment-requests.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Final Requests') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('finance') || Auth::user()->hasRole('finance_approver') || Auth::user()->hasRole('finance_cheque_prepare'))
            <x-sidebar-link :href="route('final-payment-requests.index')" :active="request()->routeIs('final-payment-requests.*')">
                <x-slot name="icon">
                    <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot>
                <span class="text-sm font-medium">{{ __('Finance Approvals') }}</span>
            </x-sidebar-link>
        @endif

        @if(Auth::user()->hasRole('admin'))
            <div class="pt-6 pb-3 border-t border-teal-600/30 dark:border-gray-700/50 mt-6">
                <x-sidebar-link :href="route('sessions.index')" :active="request()->routeIs('sessions.*')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5 flex-shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-slot>
                    <span class="text-sm font-medium">{{ __('Active Sessions') }}</span>
                </x-sidebar-link>
            </div>
        @endif
    </nav>
</aside>