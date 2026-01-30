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

    /**
     * Approve the payment document.
     */
    public function approve(Request $request, PaymentDocument $paymentDocument)
    {
        if (!Auth::user()->can('payment_documents.approve')) {
            abort(403);
        }

        if ($paymentDocument->status !== 'submitted') {
            return back()->with('error', 'Document is not in submission status.');
        }

        $paymentDocument->update([
            'status' => 'approved',
            'procurement_reviewer_id' => Auth::id(),
            'reviewed_date' => now()
        ]);

        return redirect()->route('procurement-review.index')
            ->with('success', 'Payment document approved successfully.');
    }

    /**
     * Reject the payment document.
     */
    public function reject(Request $request, PaymentDocument $paymentDocument)
    {
        if (!Auth::user()->can('payment_documents.reject')) {
            abort(403);
        }
        
        if ($paymentDocument->status !== 'submitted') {
            return back()->with('error', 'Document is not in submission status.');
        }

        // Reset to draft so procurement officer can edit and resubmit
        $paymentDocument->update([
            'status' => 'rejected',
            'procurement_reviewer_id' => Auth::id(),
            'reviewed_date' => now()
        ]);

        return redirect()->route('procurement-review.index')
            ->with('success', 'Payment document rejected.');
    }
}
