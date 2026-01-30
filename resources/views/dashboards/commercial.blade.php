@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Commercial Dashboard</h1>
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
