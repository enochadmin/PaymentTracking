<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentDocument;
use App\Models\FinalPaymentRequest;
use App\Models\Contract;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Load roles relationship to ensure hasRole() works
        $user->load('roles');
        
        // Pick the highest priority role
        if ($user->hasRole('admin')) {
            return view('dashboard');
        }
        
        if ($user->hasRole('procurement')) {
            // My documents
            $myDocuments = PaymentDocument::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            
            // Stats
            $stats = [
                'total' => PaymentDocument::where('user_id', $user->id)->count(),
                'approved' => PaymentDocument::where('user_id', $user->id)->where('status', 'approved')->count(),
                'submitted' => PaymentDocument::where('user_id', $user->id)->where('status', 'submitted')->count(),
                'rejected' => PaymentDocument::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];

            return view('dashboards.procurement', compact('myDocuments', 'stats'));
        }
        
        if ($user->hasRole('procurement_reviewer')) {
            // Documents needing review
            $pendingReviews = PaymentDocument::where('status', 'submitted')->count();
            $recentReviews = PaymentDocument::with('user')
                ->where('status', 'submitted')
                ->latest('submission_date')
                ->take(5)
                ->get();
            
            // Stats
            $stats = [
                'pending' => $pendingReviews,
                'reviewed' => PaymentDocument::whereIn('status', ['approved', 'rejected'])->count(),
                'approved' => PaymentDocument::where('status', 'approved')->count(),
                'rejected' => PaymentDocument::where('status', 'rejected')->count(),
            ];

            return view('dashboards.procurement_reviewer', compact('pendingReviews', 'recentReviews', 'stats'));
        }
        
        if ($user->hasRole('commercial')) {
            // Approved documents (eligible for contracts)
            $approvedDocs = PaymentDocument::where('status', 'approved')
                ->doesntHave('finalPaymentRequests')
                ->latest('reviewed_date')
                ->take(10)
                ->get();
                
            $recentContracts = Contract::latest()->take(5)->get();
            $myFinalRequests = FinalPaymentRequest::where('commercial_user_id', $user->id)->latest()->take(5)->get();
            
            // Stats
            $stats = [
                'pendingContracts' => PaymentDocument::where('status', 'approved')->doesntHave('finalPaymentRequests')->count(),
                'totalContracts' => Contract::count(),
                'pendingFinalRequests' => FinalPaymentRequest::where('status', 'submitted_to_finance')->count(),
                'totalFinalRequests' => FinalPaymentRequest::count(),
            ];

            return view('dashboards.commercial', compact('approvedDocs', 'recentContracts', 'myFinalRequests', 'stats'));
        }
        
        if ($user->hasRole('finance') || $user->hasRole('finance_approver')) {
            // Pending Final Requests
            $pendingRequests = FinalPaymentRequest::where('status', 'submitted_to_finance')->get();
            $recentProcessed = FinalPaymentRequest::whereIn('status', ['finance_approved', 'paid'])
                ->latest('updated_at')
                ->take(5)
                ->get();
            
            // Stats
            $stats = [
                'pendingCount' => FinalPaymentRequest::where('status', 'submitted_to_finance')->count(),
                'pendingAmount' => FinalPaymentRequest::where('status', 'submitted_to_finance')->sum('amount'),
                'paidCount' => FinalPaymentRequest::where('status', 'paid')->count(),
                'paidAmount' => FinalPaymentRequest::where('status', 'paid')->sum('amount'),
            ];

            return view('dashboards.finance', compact('pendingRequests', 'recentProcessed', 'stats'));
        }
        
        if ($user->hasRole('finance_cheque_prepare')) {
            // Pending Payment (after Finance)
            $approvedRequests = FinalPaymentRequest::where('status', 'finance_approved')->latest()->get();
            
             // Stats
            $stats = [
                 'approvedCount' => FinalPaymentRequest::where('status', 'finance_approved')->count(),
                 'approvedAmount' => FinalPaymentRequest::where('status', 'finance_approved')->sum('amount'),
                 'paidCount' => FinalPaymentRequest::where('status', 'paid')->count(),
            ];
            
            return view('dashboards.finance_cheque', compact('approvedRequests', 'stats'));
        }

        return view('dashboard');
    }
}
