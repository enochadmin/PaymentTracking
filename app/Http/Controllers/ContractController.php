<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PaymentRequest;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index()
    {
        $contracts = Contract::with(['supplier', 'project', 'paymentRequest', 'user'])
            ->latest()
            ->paginate(20);
        
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();
        $paymentRequests = PaymentRequest::whereIn('status', ['procurement_approved'])
            ->whereDoesntHave('contract')
            ->with(['supplier', 'project'])
            ->get();
        
        return view('contracts.create', compact('projects', 'suppliers', 'paymentRequests'));
    }

    /**
     * Show the form for creating a contract from a payment request.
     */
    public function createFromPaymentRequest(PaymentRequest $paymentRequest)
    {
        // Validate that the payment request is approved
        if ($paymentRequest->status !== 'procurement_approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Only approved payment requests can have contracts created.');
        }

        // Validate supplier type
        if (!in_array($paymentRequest->supplier->supplier_type, ['Subcontractor', 'Consultant'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Contracts can only be created for Subcontractor or Consultant suppliers.');
        }

        // Check if contract already exists
        if ($paymentRequest->contract) {
            return redirect()->route('contracts.show', $paymentRequest->contract)
                ->with('info', 'This payment request already has a contract.');
        }

        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();
        
        return view('contracts.create', compact('paymentRequest', 'projects', 'suppliers'));
    }

    /**
     * Store a newly created contract in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_request_id' => 'required|exists:payment_requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'project_id' => 'required|exists:projects,id',
            'contract_number' => 'required|string|unique:contracts,contract_number',
            'contract_title' => 'required|string|max:255',
            'contract_type' => 'required|in:Subcontractor,Consultant',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_terms' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'status' => 'required|in:draft,active,completed,terminated',
            'notes' => 'nullable|string',
        ]);

        // Verify payment request is approved
        $paymentRequest = PaymentRequest::findOrFail($validated['payment_request_id']);
        if ($paymentRequest->status !== 'procurement_approved') {
            return back()->with('error', 'Only approved payment requests can have contracts created.');
        }

        // Verify supplier type
        $supplier = Supplier::findOrFail($validated['supplier_id']);
        if (!in_array($supplier->supplier_type, ['Subcontractor', 'Consultant'])) {
            return back()->with('error', 'Contracts can only be created for Subcontractor or Consultant suppliers.');
        }

        // Check if contract already exists for this payment request
        if ($paymentRequest->contract) {
            return redirect()->route('contracts.show', $paymentRequest->contract)
                ->with('error', 'A contract already exists for this payment request.');
        }

        $data = $validated;
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('contract_document')) {
            $data['contract_document_path'] = $request->file('contract_document')
                ->store('contracts', 'public');
        }

        $contract = Contract::create($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Display the specified contract.
     */
    public function show(Contract $contract)
    {
        $contract->load(['supplier', 'project', 'paymentRequest', 'user']);
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit(Contract $contract)
    {
        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();
        $paymentRequests = PaymentRequest::whereIn('status', ['procurement_approved'])
            ->with(['supplier', 'project'])
            ->get();
        
        return view('contracts.edit', compact('contract', 'projects', 'suppliers', 'paymentRequests'));
    }

    /**
     * Update the specified contract in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'contract_number' => 'required|string|unique:contracts,contract_number,' . $contract->id,
            'contract_title' => 'required|string|max:255',
            'contract_type' => 'required|in:Subcontractor,Consultant',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_terms' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'status' => 'required|in:draft,active,completed,terminated',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('contract_document')) {
            $validated['contract_document_path'] = $request->file('contract_document')
                ->store('contracts', 'public');
        }

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        if ($contract->status === 'active') {
            return back()->with('error', 'Cannot delete an active contract. Please terminate it first.');
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }
}
