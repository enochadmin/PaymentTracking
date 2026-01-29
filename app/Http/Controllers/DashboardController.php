<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Load roles relationship to ensure hasRole() works
        $user->load('roles');
        
        // This logic assumes a user has one primary role for the dashboard, 
        // or we pick the highest priority one.
        
        if ($user->hasRole('admin')) {
            // Gather statistics for admin dashboard
            $stats = [
                'totalUsers' => \App\Models\User::count(),
                'totalSuppliers' => \App\Models\Supplier::count(),
                'totalProjects' => \App\Models\Project::count(),
                'totalBanks' => \App\Models\Bank::count(),
                'totalTeams' => \App\Models\Team::count(),
                'totalPaymentRequests' => PaymentRequest::count(),
                'pendingPaymentRequests' => PaymentRequest::whereIn('status', ['submitted', 'commercial_approved'])->count(),
                'paidPaymentRequests' => PaymentRequest::where('status', 'paid')->count(),
            ];
            return view('dashboard', compact('stats'));
        }
        
        if ($user->hasRole('procurement')) {
            // My requests
            $myRequests = PaymentRequest::where('user_id', $user->id)->latest()->get();
            return view('dashboards.procurement', compact('myRequests'));
        }
        
        if ($user->hasRole('commercial')) {
            // Pending Commercial Approval
            $pendingRequests = PaymentRequest::where('status', 'submitted')->latest()->get();
            
            // Approved requests eligible for contract creation (Subcontractor/Consultant only)
            $approvedRequests = PaymentRequest::where('status', 'procurement_approved')
                ->whereHas('supplier', function($query) {
                    $query->whereIn('supplier_type', ['Subcontractor', 'Consultant']);
                })
                ->with(['supplier', 'project', 'contract'])
                ->latest()
                ->get();
            
            // Recent contracts
            $recentContracts = \App\Models\Contract::with(['supplier', 'project'])
                ->latest()
                ->take(5)
                ->get();
            
            return view('dashboards.commercial', compact('pendingRequests', 'approvedRequests', 'recentContracts'));
        }
        
        if ($user->hasRole('finance')) {
             // Pending Finance Review (after Commercial)
             $pendingRequests = PaymentRequest::where('status', 'commercial_approved')->latest()->get();
             return view('dashboards.finance', compact('pendingRequests'));
        }
        
        if ($user->hasRole('finance_approver')) {
             // Maybe same as finance but final step? Or high value?
             // For now assuming they see similar to finance but maybe different actions.
             $pendingRequests = PaymentRequest::where('status', 'commercial_approved')->latest()->get();
             return view('dashboards.finance_approver', compact('pendingRequests'));
        }
        
        if ($user->hasRole('finance_cheque_prepare')) {
            // Pending Payment (after Finance)
            $approvedRequests = PaymentRequest::where('status', 'finance_approved')->latest()->get();
            return view('dashboards.finance_cheque', compact('approvedRequests'));
        }
        
        // Default fallback - provide stats for any user
        $stats = [
            'totalUsers' => \App\Models\User::count(),
            'totalSuppliers' => \App\Models\Supplier::count(),
            'totalProjects' => \App\Models\Project::count(),
            'totalBanks' => \App\Models\Bank::count(),
            'totalTeams' => \App\Models\Team::count(),
            'totalPaymentRequests' => PaymentRequest::count(),
            'pendingPaymentRequests' => PaymentRequest::whereIn('status', ['submitted', 'commercial_approved'])->count(),
            'paidPaymentRequests' => PaymentRequest::where('status', 'paid')->count(),
        ];
        return view('dashboard', compact('stats'));
    }
}
