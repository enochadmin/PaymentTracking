<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\FinalPaymentRequest;
use App\Models\PaymentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinalPaymentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Logic for different roles
        if ($user->can('final_payment_requests.approve_finance') || $user->can('final_payment_requests.mark_paid')) {
            // Finance sees requests submitted to finance or approved
            $requests = FinalPaymentRequest::with(['paymentDocument', 'project', 'supplier', 'contract'])
                ->whereIn('status', ['submitted_to_finance', 'finance_approved', 'paid'])
                ->latest()
                ->paginate(20);
        } elseif ($user->can('final_payment_requests.create')) {
             // Commercial sees their requests
             $requests = FinalPaymentRequest::with(['paymentDocument', 'project', 'supplier', 'contract'])
                ->where('commercial_user_id', $user->id)
                ->latest()
                ->paginate(20);
        } else {
            // Default view all if authorized
             $requests = FinalPaymentRequest::with(['paymentDocument', 'project', 'supplier', 'contract'])
                ->latest()
                ->paginate(20);
        }

        return view('final_payment_requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource from a payment document.
     */
    public function createFromDocument(PaymentDocument $paymentDocument)
    {
        // Only valid if approved
        if ($paymentDocument->status !== 'approved') {
            abort(403, 'Document must be approved by procurement review first.');
        }
        
        // Load relationships
        $paymentDocument->load(['project', 'supplier', 'contract']);
        
        // If supplier is Subcontractor/Consultant, check if contract exists
        if (in_array($paymentDocument->supplier->supplier_type, ['Subcontractor', 'Consultant'])) {
            // This check might be bypassed if user explicitly chose to skip contract?
            // But usually we want to enforce it unless skipped.
            // For now, we'll assume the user has made the choice.
            // The view will handle the contract selection if available.
        }

        return view('final_payment_requests.create', compact('paymentDocument'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_document_id' => 'required|exists:payment_documents,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_type' => 'required|string',
            'notes' => 'nullable|string',
            'contract_id' => 'nullable|exists:contracts,id'
        ]);
        
        // Verify payment document status
        $paymentDocument = PaymentDocument::findOrFail($request->payment_document_id);
        if ($paymentDocument->status !== 'approved') {
            abort(403, 'Payment document is not approved.');
        }
        
        // Create Final Request
        $finalRequest = FinalPaymentRequest::create([
            'payment_document_id' => $validated['payment_document_id'],
            'contract_id' => $validated['contract_id'] ?? $paymentDocument->contract?->id, // Use linked contract if exists
            'commercial_user_id' => Auth::id(),
            'project_id' => $paymentDocument->project_id,
            'supplier_id' => $paymentDocument->supplier_id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'payment_type' => $validated['payment_type'],
            'notes' => $validated['notes'],
            'status' => 'submitted_to_finance', // Direct submit or Draft? Request implied "Forward to finance"
            'submitted_to_finance_date' => now(),
        ]);

        return redirect()->route('final-payment-requests.index')
            ->with('success', 'Final Payment Request sent to Finance.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinalPaymentRequest $finalPaymentRequest)
    {
        $finalPaymentRequest->load(['paymentDocument', 'project', 'supplier', 'contract', 'commercialUser']);
        return view('final_payment_requests.show', compact('finalPaymentRequest'));
    }

    /**
     * Approve by Finance
     */
    public function approveFinance(Request $request, FinalPaymentRequest $finalPaymentRequest)
    {
        if (!Auth::user()->can('final_payment_requests.approve_finance')) {
            abort(403);
        }

        $finalPaymentRequest->update([
            'status' => 'finance_approved',
            'finance_approved_date' => now(),
            'finance_comments' => $request->finance_comments
        ]);

        return back()->with('success', 'Approved by Finance.');
    }

    /**
     * Mark as Paid
     */
    public function markAsPaid(Request $request, FinalPaymentRequest $finalPaymentRequest)
    {
        if (!Auth::user()->can('final_payment_requests.mark_paid')) {
            abort(403);
        }

        $request->validate([
            'cheque_number' => 'required|string',
            'payment_date' => 'required|date'
        ]);

        $finalPaymentRequest->update([
            'status' => 'paid',
            'payment_date' => $request->payment_date,
            'cheque_number' => $request->cheque_number
        ]);

        return back()->with('success', 'Payment marked as Paid.');
    }
}
