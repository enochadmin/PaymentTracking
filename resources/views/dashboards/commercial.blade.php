@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Commercial Dashboard</h1>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @foreach([
            ['label' => 'Total Contracts', 'value' => $stats['totalContracts'], 'subvalue' => ($myFinalRequests->first() ? $myFinalRequests->first()->currency : 'USD') . ' ' . number_format($stats['totalContractValue'], 2), 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Active Contracts', 'value' => $stats['activeContracts'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Expiring (30 Days)', 'value' => $stats['expiringContracts'], 'color' => 'red', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Pending Requests', 'value' => $stats['pendingFinalRequests'], 'color' => 'indigo', 'icon' => 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z'],
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
        <!-- Contract Status Chart -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Contract Status</h3>
            <div class="h-80">
                <canvas id="contractStatusChart"></canvas>
            </div>
        </div>

        <!-- Expiring Contracts List -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden lg:col-span-2">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Expiring Contracts</h3>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($expiringContracts as $contract)
                <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500 truncate">{{ $contract->contract_number }}</a>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $contract->contract_title }}</p>
                            <p class="text-xs text-red-500 dark:text-red-400">Expires: {{ $contract->end_date->format('M d, Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $contract->days_to_expiry }} days left</span>
                    </div>
                </li>
                @empty
                <li class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No expiring contracts found.</li>
                @endforelse
            </ul>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
                <a href="{{ route('contracts.index') }}" class="text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Recent Payment Requests and Contracts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payment Requests -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Payment Requests</h3>
                <a href="{{ route('final-payment-requests.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500">+ New Request</a>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($myFinalRequests as $req)
                <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('final-payment-requests.show', $req) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500 truncate">{{ $req->project->name }} - {{ $req->supplier->name }}</a>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $req->created_at->format('M d, Y') }} | {{ $req->currency }} {{ number_format($req->amount, 2) }}</p>
                        </div>
                        <span class="px-2 text-xs font-semibold rounded-full 
                            @if($req->status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100
                            @elseif($req->status === 'finance_approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No requests found.</li>
                @endforelse
            </ul>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
                <a href="{{ route('final-payment-requests.index') }}" class="text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
            </div>
        </div>

        <!-- Recent Contracts -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Contracts</h3>
                <a href="{{ route('contracts.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500">+ New Contract</a>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentContracts as $contract)
                <li class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-500 truncate">{{ $contract->contract_number }}</a>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $contract->contract_title }}</p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $contract->created_at->format('M d, Y') }}</span>
                    </div>
                </li>
                @empty
                <li class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No contracts found.</li>
                @endforelse
            </ul>
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-sm font-medium">
                <a href="{{ route('contracts.index') }}" class="text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('contractStatusChart')?.getContext('2d');
        if (ctx) {
            const data = @json($contractsByStatus);
            const statusColors = {
                'draft': '#9CA3AF', 'active': '#10B981', 'completed': '#3B82F6',
                'terminated': '#EF4444', 'default': '#6366F1'
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
        }
    });
</script>
@endsection