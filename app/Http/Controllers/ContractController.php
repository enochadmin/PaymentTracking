<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PaymentDocument;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with(['supplier', 'project', 'user'])->latest()->paginate(20);
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();
        return view('contracts.create', compact('projects', 'suppliers'));
    }

    /**
     * Show the form for creating a contract from a payment document.
     */
    public function createFromPaymentDocument(PaymentDocument $paymentDocument)
    {
        // 1. Check if eligible status (procurement approved)
        if ($paymentDocument->status !== 'approved') {
            return redirect()->route('dashboards.commercial')
                ->with('error', 'Only approved payment documents can be used to create contracts.');
        }

        // 2. Check supplier type
        $allowedTypes = ['Subcontractor', 'Consultant'];
        if (!in_array($paymentDocument->supplier->supplier_type, $allowedTypes)) {
             // This warning is fine, but maybe we allow consistent behavior or redirect to final payment request?
             // User requested popup skip option, so they might come here and then realize.
             // But technically we shouldn't block, just warn.
        }

        // 3. Check if contract already exists
        if ($paymentDocument->contract) {
            return redirect()->route('contracts.show', $paymentDocument->contract)
                ->with('info', 'A contract already exists for this payment document.');
        }

        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();

        return view('contracts.create', compact('paymentDocument', 'projects', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_document_id' => 'required|exists:payment_documents,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'project_id' => 'required|exists:projects,id',
            'contract_number' => 'required|string|unique:contracts,contract_number',
            'contract_title' => 'required|string',
            'contract_type' => 'required|in:Subcontractor,Consultant',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->except('contract_document');
        
        // Handle File Upload
        if ($request->hasFile('contract_document')) {
            $data['contract_document_path'] = $request->file('contract_document')->store('contracts', 'public');
        }

        $data['user_id'] = Auth::id();
        $data['status'] = 'draft'; 

        $contract = Contract::create($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $contract->load(['paymentDocument', 'supplier', 'project', 'user']);
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $projects = Project::all();
        $suppliers = Supplier::whereIn('supplier_type', ['Subcontractor', 'Consultant'])->get();
        return view('contracts.edit', compact('contract', 'projects', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'project_id' => 'required|exists:projects,id',
            'contract_number' => 'required|string|unique:contracts,contract_number,' . $contract->id,
            'contract_title' => 'required|string',
            'contract_type' => 'required|in:Subcontractor,Consultant',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_terms' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'status' => 'required|in:draft,active,completed,terminated',
            'contract_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->except('contract_document');

        // Handle File Upload
        if ($request->hasFile('contract_document')) {
             if ($contract->contract_document_path) {
                Storage::disk('public')->delete($contract->contract_document_path);
            }
            $data['contract_document_path'] = $request->file('contract_document')->store('contracts', 'public');
        }

        $contract->update($data);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contract updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        if ($contract->status === 'active') {
             return back()->with('error', 'Cannot delete active contracts.');
        }

        if ($contract->contract_document_path) {
            Storage::disk('public')->delete($contract->contract_document_path);
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }
}
