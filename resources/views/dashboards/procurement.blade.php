@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Procurement Dashboard</h1>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Total Received', 'value' => $stats['total'], 'color' => 'indigo', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Total Value Prepared', 'value' => ($myDocuments->first() ? $myDocuments->first()->currency : 'USD') . ' ' . number_format($stats['totalAmount'], 2), 'color' => 'emerald', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Top Responsible', 'value' => $stats['topPerson'], 'subvalue' => $stats['topPersonCount'] . ' Documents', 'color' => 'purple', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
            ['label' => 'Approved Docs', 'value' => $stats['approved'], 'color' => 'blue', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
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
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $card['value'] }}</dd>
                            @if(isset($card['subvalue']))
                                <p class="text-xs font-semibold text-{{ $card['color'] }}-500 dark:text-{{ $card['color'] }}-400 mt-1">{{ $card['subvalue'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts and Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <!-- Document Status Chart -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Document Status</h3>
            <div class="h-64">
                <canvas id="documentStatusChart"></canvas>
            </div>
        </div>
        
        <!-- Suppliers Chart -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Suppliers by Type</h3>
            <div class="h-64">
                <canvas id="suppliersChart"></canvas>
            </div>
        </div>

        <!-- Activity Chart -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Activity (30 Days)</h3>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Top Responsible Persons -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top Responsible Persons</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Person</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Document Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php $totalDocs = $stats['total'] > 0 ? $stats['total'] : 1; @endphp
                        @forelse($topPersons as $person)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $person['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $person['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium">{{ round(($person['count'] / $totalDocs) * 100, 1) }}%</span>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 max-w-[180px]">
                                        <div class="bg-purple-600 h-2.5 rounded-full transition-all" style="width: {{ ($person['count'] / $totalDocs) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No responsible persons found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
                <a href="#" class="text-indigo-600 hover:text-indigo-500">View more &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Recent Documents Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Documents</h3>
            <a href="{{ route('payment-documents.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500">+ New Document</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($myDocuments as $doc)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $doc->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $doc->project->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $doc->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($doc->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100
                                @elseif($doc->status === 'submitted') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100
                                @elseif($doc->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100
                                @endif">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('payment-documents.show', $doc) }}" class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No documents found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
            <a href="{{ route('payment-documents.index') }}" class="text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusColors = {
            'draft': '#9CA3AF', 'submitted': '#F59E0B', 'approved': '#10B981',
            'rejected': '#EF4444', 'default': '#6366F1'
        };

        // 1. Document Status Chart
        const ctxStatus = document.getElementById('documentStatusChart')?.getContext('2d');
        if (ctxStatus) {
            const data = @json($statusDistribution);
            const labels = Object.keys(data);
            const values = Object.values(data);
            const colors = labels.map(status => statusColors[status.toLowerCase()] || statusColors['default']);

            new Chart(ctxStatus, {
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
                        legend: { position: 'bottom', labels: { color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#374151' } }
                    }
                }
            });
        }

        // 2. Suppliers Chart
        const ctxSuppliers = document.getElementById('suppliersChart')?.getContext('2d');
        if (ctxSuppliers) {
            const data = @json($suppliersByType);
            const labels = Object.keys(data);
            const values = Object.values(data);
            
            new Chart(ctxSuppliers, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B', '#EF4444'], // Blue, Purple, Green, Yellow, Red
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#374151' } }
                    }
                }
            });
        }

        // 3. Activity Chart
        const ctxActivity = document.getElementById('activityChart')?.getContext('2d');
        if (ctxActivity) {
            const data = @json($activityData);
            
            new Chart(ctxActivity, {
                type: 'bar',
                data: {
                    labels: data.dates,
                    datasets: [
                        {
                            label: 'Received',
                            data: data.received,
                            backgroundColor: '#6366F1', // Indigo
                        },
                        {
                            label: 'Reviewed',
                            data: data.reviewed,
                            backgroundColor: '#10B981', // Emerald
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563' } },
                        y: { ticks: { color: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#4B5563' }, beginAtZero: true }
                    },
                    plugins: {
                        legend: { position: 'bottom', labels: { color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#374151' } }
                    }
                }
            });
        }
    });
</script>
@endsection