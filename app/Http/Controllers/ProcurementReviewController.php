<?php

namespace App\Http\Controllers;

use App\Models\PaymentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementReviewController extends Controller
{
    /**
     * Display a listing of submitted documents awaiting review.
     */
    public function index()
    {
        // Check permission
        if (!Auth::user()->can('payment_documents.review')) {
            abort(403);
        }

        // Get submitted documents
        $documents = PaymentDocument::with(['project', 'supplier', 'user'])
            ->where('status', 'submitted')
            ->latest('submission_date')
            ->paginate(20);
            
        return view('procurement_review.index', compact('documents'));
    }

    /**
     * Show the specified document for review.
     */
    public function show(PaymentDocument $paymentDocument)
    {
        if (!Auth::user()->can('payment_documents.review')) {
            abort(403);
        }

        $paymentDocument->load(['project', 'supplier', 'user']);
        return view('procurement_review.show', compact('paymentDocument'));
    }

    public function approve(Request $request, PaymentDocument $paymentDocument)
    {
        if (!Auth::user()->can('payment_documents.approve')) {
            abort(403);
        }

        if ($paymentDocument->status !== 'submitted') {
            return back()->with('error', 'Document is not in submission status.');
        }

        // Validate optional fields
        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:5000',
            'review_date' => 'nullable|date',
        ]);

        $paymentDocument->update([
            'status' => 'approved',
            'procurement_reviewer_id' => Auth::id(),
            'reviewed_date' => $validated['review_date'] ?? now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return redirect()->route('procurement-review.index')
            ->with('success', 'Payment document approved successfully.');
    }

    public function reject(Request $request, PaymentDocument $paymentDocument)
    {
        if (!Auth::user()->can('payment_documents.reject')) {
            abort(403);
        }
        
        if ($paymentDocument->status !== 'submitted') {
            return back()->with('error', 'Document is not in submission status.');
        }

        // Validate required and optional fields
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:5000',
            'review_notes' => 'nullable|string|max:5000',
            'review_date' => 'nullable|date',
        ]);

        // Reset to rejected so procurement officer can see the reason
        $paymentDocument->update([
            'status' => 'rejected',
            'procurement_reviewer_id' => Auth::id(),
            'reviewed_date' => $validated['review_date'] ?? now(),
            'rejection_reason' => $validated['rejection_reason'],
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        return redirect()->route('procurement-review.index')
            ->with('success', 'Payment document rejected.');
    }
}
