@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Procurement Dashboard</h1>
    </div>

    <!-- Stats Grid -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Documents -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                 <div class="bg-indigo-100 text-indigo-600 rounded-full p-3 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                     <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Recieved </h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Value Prepared -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                 <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Value Prepared</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $myDocuments->first() ? $myDocuments->first()->currency : 'USD' }} {{ number_format($stats['totalAmount'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Top Responsible Person -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="bg-purple-100 text-purple-600 rounded-full p-3 mr-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Top Responsible</h4>
                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate w-32 md:w-40" title="{{ $stats['topPerson'] }}">{{ $stats['topPerson'] }}</p>
                    <p class="text-xs text-purple-500 font-semibold">{{ $stats['topPersonCount'] }} Documents</p>
                </div>
            </div>
        </div>

         <!-- Approved Count (Simplified) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
             <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                     <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                 <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Approved Docs</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['approved'] }}</p>
                </div>
             </div>
        </div>
    </div>

    <!-- Charts and Top Persons Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Status Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-1">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Document Status</h3>
            <div class="relative h-64 w-full">
                <canvas id="documentStatusChart"></canvas>
            </div>
        </div>

        <!-- Top Responsible Persons Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Documents by Responsible Person</h3>
            <div class="overflow-y-auto max-h-64">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                         <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Value</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($documentsByResponsiblePerson as $person)
                        <tr>
                            <td class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $person->responsible_person }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($person->total_amount, 2) }}
                            </td>
                            <td class="px-3 py-2 text-right text-sm text-gray-500 dark:text-gray-400">
                                {{ $person->count }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Recent Documents -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                My Recent Payment Documents
            </h3>
            <a href="{{ route('payment-documents.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 px-4 rounded shadow">
                + Create New
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($myDocuments as $doc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $doc->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $doc->project->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $doc->supplier->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($doc->status === 'approved') bg-green-100 text-green-800
                                @elseif($doc->status === 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($doc->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('payment-documents.show', $doc) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No documents found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6">
            <a href="{{ route('payment-documents.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('documentStatusChart').getContext('2d');
        const data = @json($statusDistribution);
        
        // Define colors for statuses
        const statusColors = {
            'draft': '#9CA3AF',      // Gray
            'submitted': '#F59E0B',  // Yellow
            'approved': '#10B981',   // Green
            'rejected': '#EF4444',   // Red
            'default': '#6366F1'     // Indigo
        };

        const labels = Object.keys(data);
        const values = Object.values(data);
        const colors = labels.map(status => statusColors[status.toLowerCase()] || statusColors['default']);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#374151'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
