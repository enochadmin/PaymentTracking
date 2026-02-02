@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Commercial Dashboard</h1>
    </div>

    <!-- Contract Statistics & Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Metric Cards -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Contracts</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['totalContracts'] }}</p>
                    <p class="text-xs text-blue-500 font-semibold">{{ $myFinalRequests->first() ? $myFinalRequests->first()->currency : 'USD' }} {{ number_format($stats['totalContractValue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Contracts</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['activeContracts'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
            <div class="flex items-center">
                <div class="bg-red-100 text-red-600 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Expiring (30 Days)</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['expiringContracts'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-5">
             <div class="flex items-center">
                <div class="bg-indigo-100 text-indigo-600 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                </div>
                 <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Requests</h4>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['pendingFinalRequests'] }}</p>
                </div>
             </div>
        </div>
    </div>

    <!-- Charts and Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Pie Chart -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-1">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Contract Status</h3>
            <div class="relative h-64 w-full">
                <canvas id="contractStatusChart"></canvas>
            </div>
        </div>

        <!-- Expiring Contracts List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-2">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Contracts Expiring Soon</h3>
            <div class="overflow-y-auto max-h-64">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                         <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title/Supplier</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Date</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($expiringContracts as $contract)
                        <tr>
                            <td class="px-3 py-2">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100 truncate w-32 md:w-48">{{ $contract->contract_title }}</span>
                                <span class="block text-xs text-gray-500">{{ $contract->supplier->name }}</span>
                            </td>
                            <td class="px-3 py-2 text-sm text-red-600 font-medium">
                                {{ $contract->end_date->format('M d, Y') }}
                                <span class="text-xs text-gray-500 block">({{ $contract->end_date->diffForHumans() }})</span>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('contracts.show', $contract) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500">No contracts expiring within 30 days.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
     <!-- Contracts by Supplier & Recent -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Top Suppliers -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Contracts by Supplier (Top 5)</h3>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($contractsBySupplier as $item)
                <li class="py-3 flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->supplier->name }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        {{ $item->count }} Contract{{ $item->count > 1 ? 's' : '' }}
                    </span>
                </li>
                @empty
                 <li class="py-3 text-sm text-gray-500">No data available.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Approved Documents (Eligible for Contract / Final Request) -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Approved Payment Documents
                <span class="text-sm font-normal text-gray-500 ml-2">(Ready for Contract or Finance)</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Approved Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($approvedDocs as $doc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $doc->reviewed_date ? $doc->reviewed_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $doc->project->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $doc->supplier->name }}
                            <span class="text-xs block text-gray-400">{{ $doc->supplier->supplier_type }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $doc->currency }} {{ number_format($doc->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            @if(in_array($doc->supplier->supplier_type, ['Subcontractor', 'Consultant']))
                                @if(!$doc->contract)
                                    <a href="{{ route('contracts.createFromPaymentDocument', $doc) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs">Create Contract</a>
                                    <!-- Skip Option -->
                                    <button onclick="if(confirm('Are you sure you want to skip contract creation for a Subcontractor/Consultant?')) window.location.href='{{ route('final-payment-requests.createFromDocument', $doc) }}'" class="text-gray-500 hover:text-gray-700 text-xs underline">Skip</button>
                                @else
                                    <span class="text-green-600 text-xs">Contract Created</span>
                                    <a href="{{ route('final-payment-requests.createFromDocument', $doc) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs">Final Request</a>
                                @endif
                            @else
                                <!-- Supply Only -->
                                <a href="{{ route('final-payment-requests.createFromDocument', $doc) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs" title="Submit to Finance">Process Payment</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No approved documents pending action.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- My Final Requests -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                    My Submitted Requests
                </h3>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($myFinalRequests as $req)
                <li class="px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600 truncate">{{ $req->project->name }} - {{ $req->supplier->name }}</p>
                            <p class="text-xs text-gray-500">{{ $req->created_at->format('M d, Y') }} | {{ $req->currency }} {{ number_format($req->amount, 2) }}</p>
                        </div>
                        <span class="px-2 text-xs font-semibold rounded-full 
                            @if($req->status === 'paid') bg-green-100 text-green-800
                            @elseif($req->status === 'finance_approved') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-4 py-4 text-sm text-gray-500">No requests found.</li>
                @endforelse
            </ul>
             <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6">
                <a href="{{ route('final-payment-requests.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
            </div>
        </div>

        <!-- Recent Contracts -->
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
             <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                    Recent Contracts
                </h3>
                <a href="{{ route('contracts.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">+ New Contract</a>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recentContracts as $contract)
                <li class="px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <a href="{{ route('contracts.show', $contract) }}" class="text-sm font-medium text-indigo-600 hover:underline truncate">{{ $contract->contract_number }}</a>
                            <p class="text-xs text-gray-500">{{ $contract->contract_title }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $contract->created_at->format('M d, Y') }}</span>
                    </div>
                </li>
                @empty
                <li class="px-4 py-4 text-sm text-gray-500">No contracts found.</li>
                @endforelse
            </ul>
             <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6">
                <a href="{{ route('contracts.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('contractStatusChart').getContext('2d');
        const data = @json($contractsByStatus);
        
        // Define colors for statuses
        const statusColors = {
            'draft': '#9CA3AF',      // Gray
            'active': '#10B981',     // Green
            'completed': '#3B82F6',  // Blue
            'terminated': '#EF4444', // Red
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
