<?php

namespace App\Http\Controllers;

use App\Models\PaymentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovedDocumentController extends Controller
{
    /**
     * Display a listing of approved payment documents for commercial users.
     */
    public function index()
    {
        // Check if user has commercial role (or permission)
        if (!Auth::user()->hasRole('commercial')) {
            abort(403);
        }

        $approvedDocuments = PaymentDocument::with(['project', 'supplier', 'user', 'contract'])
            ->where('status', 'approved')
            ->latest('reviewed_date')
            ->paginate(20);

        return view('commercial.approved_documents', compact('approvedDocuments'));
    }
}
