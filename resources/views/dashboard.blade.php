@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Admin Dashboard</h1>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'color' => 'indigo', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Total Suppliers', 'value' => $stats['totalSuppliers'] ?? 0, 'color' => 'emerald', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['label' => 'Total Projects', 'value' => $stats['totalProjects'] ?? 0, 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Total Banks', 'value' => $stats['totalBanks'] ?? 0, 'color' => 'amber', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
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

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Users by Role</h3>
            <div class="h-80">
                <canvas id="usersByRoleChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Documents by Status</h3>
            <div class="h-80">
                <canvas id="docsByStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Projects Table -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top Projects by Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Document Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php $totalDocs = $stats['totalPaymentRequests'] > 0 ? $stats['totalPaymentRequests'] : 1; @endphp
                    @forelse($topProjects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $project['count'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-3">
                                <span class="font-medium">{{ round(($project['count'] / $totalDocs) * 100, 1) }}%</span>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 max-w-[180px]">
                                    <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ ($project['count'] / $totalDocs) * 100 }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No project activity found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const legendColor = isDark ? '#D1D5DB' : '#374151';

        // Users by Role Chart
        const ctxUsers = document.getElementById('usersByRoleChart')?.getContext('2d');
        if (ctxUsers) {
            const usersData = @json($usersByRole);
            new Chart(ctxUsers, {
                type: 'pie',
                data: {
                    labels: Object.keys(usersData),
                    datasets: [{
                        data: Object.values(usersData),
                        backgroundColor: ['#f0bdbdff', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: legendColor } }
                    }
                }
            });
        }

        // Documents by Status Chart
        const ctxDocs = document.getElementById('docsByStatusChart')?.getContext('2d');
        if (ctxDocs) {
            const docsData = @json($docsByStatus);
            const statusColors = {
                'draft': '#9CA3AF', 'submitted': '#F59E0B', 'approved': '#10B981',
                'rejected': '#EF4444', 'pending': '#F59E0B', 'paid': '#3B82F6'
            };
            const docLabels = Object.keys(docsData);
            const docColors = docLabels.map(l => statusColors[l.toLowerCase()] || '#6366F1');

            new Chart(ctxDocs, {
                type: 'bar',
                data: {
                    labels: docLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                    datasets: [{
                        label: 'Count',
                        data: Object.values(docsData),
                        backgroundColor: docColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { color: legendColor } },
                        y: { ticks: { color: legendColor }, beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endsection
