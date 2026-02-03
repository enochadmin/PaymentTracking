@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Finance Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Pending Requests', 'value' => $stats['pendingCount'], 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Pending Amount', 'value' => number_format($stats['pendingAmount']), 'color' => 'indigo', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Paid (Qty)', 'value' => $stats['paidCount'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Paid Amount', 'value' => number_format($stats['paidAmount']), 'color' => 'teal', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $card)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-{{ $card['color'] }}-600 rounded-lg p-3">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Pending Requests Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden mb-10">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pending Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pendingRequests as $req)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $req->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $req->project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $req->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $req->commercialUser->name ?? 'Commercial' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $req->currency }} {{ number_format($req->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('final-payment-requests.show', $req) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs">Review & Approve</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No pending requests.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recently Processed -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recently Processed</h3>
        </div>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($recentProcessed as $req)
            <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ $req->project->name }} - {{ $req->supplier->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Processed: {{ $req->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    <span class="px-2 text-xs font-semibold rounded-full 
                        @if($req->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100
                        @elseif($req->status === 'finance_approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                    </span>
                </div>
            </li>
            @empty
            <li class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No recently processed requests.</li>
            @endforelse
        </ul>
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
            <a href="{{ route('final-payment-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
        </div>
    </div>
</div>
@endsection