@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Procurement Reviewer Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Review and manage procurement documents</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Total Received', 'value' => $stats['total_received'], 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Pending Reviews', 'value' => $stats['pending'], 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Approved', 'value' => $stats['approved'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Rejected', 'value' => $stats['rejected'], 'color' => 'red', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $card)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-{{ $card['color'] }}-600 rounded-lg p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <dt class="text-sm font-medium text-gray-600 dark:text-gray-300 truncate">{{ $card['label'] }}</dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $card['value'] }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Deadline Tracking Section -->
    <div class="mb-10">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Deadline Tracking</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Expired Deadlines -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border-l-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Expired</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100">
                            {{ $stats['expired_deadline'] }}
                        </span>
                    </div>
                    @if($expiredDocuments->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No expired deadlines</p>
                    @else
                        <div class="space-y-3">
                            @foreach($expiredDocuments->take(3) as $doc)
                                <div class="border-l-2 border-red-300 pl-3">
                                    <a href="{{ route('procurement-review.show', $doc) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $doc->project->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Due: {{ $doc->review_deadline->format('M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                            @if($expiredDocuments->count() > 3)
                                <a href="{{ route('procurement-review.index') }}" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                    View all {{ $expiredDocuments->count() }} expired →
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Near Deadline -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border-l-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Near Deadline</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100">
                            {{ $stats['near_deadline'] }}
                        </span>
                    </div>
                    @if($nearDeadlineDocuments->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No near deadlines</p>
                    @else
                        <div class="space-y-3">
                            @foreach($nearDeadlineDocuments->take(3) as $doc)
                                <div class="border-l-2 border-yellow-300 pl-3">
                                    <a href="{{ route('procurement-review.show', $doc) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $doc->project->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Due: {{ $doc->review_deadline->format('M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                            @if($nearDeadlineDocuments->count() > 3)
                                <a href="{{ route('procurement-review.index') }}" class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                                    View all {{ $nearDeadlineDocuments->count() }} →
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- On Track -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">On Track</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100">
                            {{ $stats['on_track'] }}
                        </span>
                    </div>
                    @if($onTrackDocuments->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No documents on track</p>
                    @else
                        <div class="space-y-3">
                            @foreach($onTrackDocuments as $doc)
                                <div class="border-l-2 border-green-300 pl-3">
                                    <a href="{{ route('procurement-review.show', $doc) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $doc->project->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Due: {{ $doc->review_deadline->format('M d, Y') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden mb-10">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Document Status Overview</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $total = $stats['total_received'] ?: 1; // Avoid division by zero
                    $statusData = [
                        ['label' => 'Pending Review', 'count' => $stats['pending'], 'color' => 'amber'],
                        ['label' => 'Approved', 'count' => $stats['approved'], 'color' => 'emerald'],
                        ['label' => 'Rejected', 'count' => $stats['rejected'], 'color' => 'red'],
                    ];
                @endphp
                @foreach($statusData as $status)
                    @php
                        $percentage = ($status['count'] / $total) * 100;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $status['label'] }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $status['count'] }} ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-{{ $status['color'] }}-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tabbed Documents Section -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px" x-data="{ activeTab: 'pending' }">
                <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Pending Review ({{ $recentReviews->count() }})
                </button>
                <button @click="activeTab = 'approved'" 
                    :class="activeTab === 'approved' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Recently Approved ({{ $recentlyApproved->count() }})
                </button>
                <button @click="activeTab = 'rejected'" 
                    :class="activeTab === 'rejected' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400'"
                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                    Recently Rejected ({{ $recentlyRejected->count() }})
                </button>
            </nav>
        </div>

        <div x-data="{ activeTab: 'pending' }">
            <!-- Pending Tab -->
            <div x-show="activeTab === 'pending'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deadline</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentReviews as $doc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->submission_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $doc->project->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->supplier->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $doc->currency }} {{ number_format($doc->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($doc->review_deadline)
                                    @php
                                        $status = $doc->deadline_status;
                                        $colors = [
                                            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                            'near' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                                            'on_track' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $colors[$status] ?? '' }}">
                                        {{ $doc->review_deadline->format('M d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('procurement-review.show', $doc) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs transition-colors">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No pending reviews.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Approved Tab -->
            <div x-show="activeTab === 'approved'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reviewed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentlyApproved as $doc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->reviewed_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $doc->project->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->supplier->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $doc->currency }} {{ number_format($doc->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->user->name }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No recently approved documents.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Rejected Tab -->
            <div x-show="activeTab === 'rejected'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reviewed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reason</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentlyRejected as $doc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->reviewed_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $doc->project->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $doc->supplier->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $doc->currency }} {{ number_format($doc->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="line-clamp-2">{{ Str::limit($doc->rejection_reason, 50) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No recently rejected documents.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium border-t border-gray-200 dark:border-gray-600">
            <a href="{{ route('procurement-review.index') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View all documents &rarr;</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
