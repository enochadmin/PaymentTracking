<?php

namespace App\Http\Controllers;

use App\Models\PaymentDocument;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get currenct user
        $user = Auth::user();
        
        // If user has view_all permission, show all, otherwise show own
        if ($user->can('payment_documents.view_all')) {
            $paymentDocuments = PaymentDocument::with(['project', 'supplier', 'user'])
                ->latest()
                ->paginate(20);
        } else {
            $paymentDocuments = PaymentDocument::with(['project', 'supplier'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(20);
        }
        
        return view('payment_documents.index', compact('paymentDocuments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $suppliers = Supplier::all();
        return view('payment_documents.create', compact('projects', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_type' => 'required|string',
            'reason_for_payment' => 'required|string',
            'responsible_person' => 'required|string',
            'received_date' => 'required|date',
            'submission_date' => 'required|date',
            'cost_type' => 'required|string',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'delivery_note' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'submit_action' => 'required|in:draft,submit'
        ]);

        $data = $request->except(['invoice', 'delivery_note', 'submit_action']);
        
        // Handle File Uploads
        if ($request->hasFile('invoice')) {
            $data['invoice_path'] = $request->file('invoice')->store('invoices', 'public');
        }
        
        if ($request->hasFile('delivery_note')) {
            $data['delivery_note_path'] = $request->file('delivery_note')->store('delivery_notes', 'public');
        }

        $data['user_id'] = Auth::id();
        
        // Set status based on action
        if ($request->submit_action === 'submit') {
            $data['status'] = 'submitted';
            $data['submission_date'] = now();
        } else {
            $data['status'] = 'draft';
        }

        PaymentDocument::create($data);

        return redirect()->route('payment-documents.index')
            ->with('success', $request->submit_action === 'submit' ? 'Payment document submitted for review.' : 'Draft saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentDocument $paymentDocument)
    {
        $this->authorizeAccess($paymentDocument);
        $paymentDocument->load(['project', 'supplier', 'user', 'reviewer']);
        return view('payment_documents.show', compact('paymentDocument'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentDocument $paymentDocument)
    {
        // Only draft documents can be edited
        if ($paymentDocument->status !== 'draft') {
            abort(403, 'Only draft documents can be edited.');
        }
        
        $this->authorizeAccess($paymentDocument);
        
        $projects = Project::all();
        $suppliers = Supplier::all();
        return view('payment_documents.edit', compact('paymentDocument', 'projects', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentDocument $paymentDocument)
    {
        // Only draft documents can be updated
        if ($paymentDocument->status !== 'draft') {
            abort(403, 'Only draft documents can be updated.');
        }
        
        $this->authorizeAccess($paymentDocument);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_type' => 'required|string',
            'reason_for_payment' => 'required|string',
            'responsible_person' => 'required|string',
            'received_date' => 'required|date',
            'submission_date' => 'required|date',
            'cost_type' => 'required|string',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'delivery_note' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'submit_action' => 'required|in:draft,submit'
        ]);

        $data = $request->except(['invoice', 'delivery_note', 'submit_action']);

        // Handle File Uploads
        if ($request->hasFile('invoice')) {
            // Delete old file
            if ($paymentDocument->invoice_path) {
                Storage::disk('public')->delete($paymentDocument->invoice_path);
            }
            $data['invoice_path'] = $request->file('invoice')->store('invoices', 'public');
        }
        
        if ($request->hasFile('delivery_note')) {
             // Delete old file
             if ($paymentDocument->delivery_note_path) {
                Storage::disk('public')->delete($paymentDocument->delivery_note_path);
            }
            $data['delivery_note_path'] = $request->file('delivery_note')->store('delivery_notes', 'public');
        }
        
        // Set status based on action
        if ($request->submit_action === 'submit') {
            $data['status'] = 'submitted';
            $data['submission_date'] = now();
        }

        $paymentDocument->update($data);

        return redirect()->route('payment-documents.index')
            ->with('success', $request->submit_action === 'submit' ? 'Payment document submitted for review.' : 'Draft updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentDocument $paymentDocument)
    {
        if ($paymentDocument->status !== 'draft') {
            abort(403, 'Cannot delete submitted documents.');
        }
        
        $this->authorizeAccess($paymentDocument);
        
        // Delete files
        if ($paymentDocument->invoice_path) {
            Storage::disk('public')->delete($paymentDocument->invoice_path);
        }
        if ($paymentDocument->delivery_note_path) {
            Storage::disk('public')->delete($paymentDocument->delivery_note_path);
        }
        
        $paymentDocument->delete();
        
        return redirect()->route('payment-documents.index')
            ->with('success', 'Payment document deleted successfully.');
    }
    
    /**
     * Check authorization
     */
    private function authorizeAccess($document)
    {
        $user = Auth::user();
        if ($document->user_id !== $user->id && !$user->can('payment_documents.view_all')) {
            abort(403);
        }
    }
}
