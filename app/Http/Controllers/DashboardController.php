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
             // Admin Stats
            $stats = [
                'totalUsers' => \App\Models\User::count(),
                'totalSuppliers' => \App\Models\Supplier::count(),
                'totalProjects' => \App\Models\Project::count(),
                'totalBanks' => \App\Models\Bank::count(),
                'totalTeams' => \App\Models\Team::count(),
                'totalPaymentRequests' => PaymentDocument::count(),
                'pendingPaymentRequests' => PaymentDocument::where('status', 'submitted')->count(), // Or FinalRequests
                'paidPaymentRequests' => FinalPaymentRequest::where('status', 'paid')->count(),
            ];

            // Charts Data
            
            // 1. Users by Role
            // This requires a join with roles table if using spatie/laravel-permission or similar, 
            // but for simplicity assuming we might just count users for now or use a relationship.
            // If User model has roles relationship:
            $usersByRole = \App\Models\User::get()->pluck('roles')->flatten()->pluck('name')->countBy();
            
            // 2. Payment Documents by Status
            $docsByStatus = PaymentDocument::select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');
                
             // 3. Projects with most documents (Top 5)
            $topProjects = PaymentDocument::select('project_id', \DB::raw('count(*) as count'))
                ->groupBy('project_id')
                ->with('project')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function($item) {
                    return ['name' => $item->project->name ?? 'Unknown', 'count' => $item->count];
                });

            return view('dashboard', compact('stats', 'usersByRole', 'docsByStatus', 'topProjects'));
        }
        
        if ($user->hasRole('procurement')) {
            // My documents
            $myDocuments = PaymentDocument::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
            
            // Stats
            // 1. Total Received Documents & Total Amount
            $totalReceived = PaymentDocument::where('user_id', $user->id)->count();
            $totalAmount = PaymentDocument::where('user_id', $user->id)->sum('amount');
            
            // 2. Top Responsible Person
            // We find the most frequent name in the 'responsible_person' column for this user's documents
            $topResponsiblePerson = PaymentDocument::where('user_id', $user->id)
                ->select('responsible_person', \DB::raw('count(*) as count'))
                ->groupBy('responsible_person')
                ->orderByDesc('count')
                ->first();
                
            // 3. Document Status Distribution (Pie Chart)
            $statusDistribution = PaymentDocument::where('user_id', $user->id)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
                
            // 4. Documents by Responsible Person (List)
            // 4. Documents by Responsible Person (List)
            $topPersons = PaymentDocument::where('user_id', $user->id)
                ->select('responsible_person', \DB::raw('count(*) as count'))
                ->groupBy('responsible_person')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function($item) {
                     return [
                        'name' => $item->responsible_person,
                        'count' => $item->count
                    ];
                });
            
            $stats = [
                'total' => $totalReceived,
                'totalAmount' => $totalAmount,
                'topPerson' => $topResponsiblePerson ? $topResponsiblePerson->responsible_person : 'N/A',
                'topPersonCount' => $topResponsiblePerson ? $topResponsiblePerson->count : 0,
                'approved' => PaymentDocument::where('user_id', $user->id)->where('status', 'approved')->count(),
                'submitted' => PaymentDocument::where('user_id', $user->id)->where('status', 'submitted')->count(),
                'rejected' => PaymentDocument::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];

            // 5. Suppliers by Type (Chart)
            $suppliersByType = \App\Models\Supplier::selectRaw('supplier_type, count(*) as count')
                ->groupBy('supplier_type')
                ->pluck('count', 'supplier_type');

            // 6. Document Activity (Received vs Reviewed over time - last 30 days)
            $startDate = now()->subDays(30);
            
            $receivedDaily = PaymentDocument::where('user_id', $user->id)
                ->where('received_date', '>=', $startDate)
                ->selectRaw('DATE(received_date) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();

            $reviewedDaily = PaymentDocument::where('user_id', $user->id)
                ->where('reviewed_date', '>=', $startDate)
                ->selectRaw('DATE(reviewed_date) as date, count(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date')
                ->toArray();

            // Merge dates for x-axis
            $allDates = array_unique(array_merge(array_keys($receivedDaily), array_keys($reviewedDaily)));
            sort($allDates);
            
            $activityData = [
                'dates' => $allDates,
                'received' => [],
                'reviewed' => []
            ];

            foreach ($allDates as $date) {
                $activityData['received'][] = $receivedDaily[$date] ?? 0;
                $activityData['reviewed'][] = $reviewedDaily[$date] ?? 0;
            }

            return view('dashboards.procurement', compact(
                'myDocuments', 
                'stats',
                'statusDistribution',
                'topPersons',
                'suppliersByType',
                'activityData'
            ));
        }
        
        if ($user->hasRole('procurement_reviewer')) {
            // Documents needing review
            $pendingReviews = PaymentDocument::where('status', 'submitted')->count();
            
            // Comprehensive Statistics
            $stats = [
                // Total counts
                'total_received' => PaymentDocument::count(),
                'pending' => PaymentDocument::where('status', 'submitted')->count(),
                'approved' => PaymentDocument::where('status', 'approved')->count(),
                'rejected' => PaymentDocument::where('status', 'rejected')->count(),
                
                // Deadline tracking
                'expired_deadline' => PaymentDocument::where('status', 'submitted')
                    ->expiredDeadline()
                    ->count(),
                'near_deadline' => PaymentDocument::where('status', 'submitted')
                    ->nearDeadline()
                    ->count(),
                'on_track' => PaymentDocument::where('status', 'submitted')
                    ->onTrackDeadline()
                    ->count(),
            ];
            
            // Documents with expired deadlines (urgent)
            $expiredDocuments = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'submitted')
                ->expiredDeadline()
                ->orderBy('review_deadline')
                ->get();
            
            // Documents near deadline (warning)
            $nearDeadlineDocuments = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'submitted')
                ->nearDeadline()
                ->orderBy('review_deadline')
                ->get();
            
            // Documents on track
            $onTrackDocuments = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'submitted')
                ->onTrackDeadline()
                ->orderBy('review_deadline')
                ->take(5)
                ->get();
            
            // Recent reviews (pending)
            $recentReviews = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'submitted')
                ->latest('submission_date')
                ->take(10)
                ->get();
            
            // Recently approved
            $recentlyApproved = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'approved')
                ->latest('reviewed_date')
                ->take(5)
                ->get();
            
            // Recently rejected
            $recentlyRejected = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'rejected')
                ->latest('reviewed_date')
                ->take(5)
                ->get();

            return view('dashboards.procurement_reviewer', compact(
                'pendingReviews', 
                'stats',
                'expiredDocuments',
                'nearDeadlineDocuments',
                'onTrackDocuments',
                'recentReviews',
                'recentlyApproved',
                'recentlyRejected'
            ));
        }
        
        if ($user->hasRole('commercial')) {
            // ALL Approved documents (available for contract creation)
            $approvedDocs = PaymentDocument::with(['project', 'supplier', 'user'])
                ->where('status', 'approved')
                ->latest('reviewed_date')
                ->get();
                
            $recentContracts = Contract::latest()->take(5)->get();
            $myFinalRequests = FinalPaymentRequest::where('commercial_user_id', $user->id)->latest()->take(5)->get();
            
            // --- Advanced Stats for Commercial Dashboard ---
            
            // 1. Total Contracts & Value
            $totalContractsCount = Contract::count();
            $totalContractsValue = Contract::sum('contract_value');
            
            // 2. Active Contracts
            $activeContractsCount = Contract::where('status', 'active')->count();
            
            // 3. Expiring Contracts (Active & End Date within 30 days)
            $expiringContractsCount = Contract::where('status', 'active')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(30))
                ->count();
                
            $expiringContracts = Contract::where('status', 'active')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(30))
                ->orderBy('end_date')
                ->take(5)
                ->get();

            // 4. Contracts by Status (For Pie Chart)
            // We need a collection like: ['active' => 10, 'completed' => 5, ...]
            $contractsByStatus = Contract::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');
                
            // 5. Contracts by Supplier (Top 5 by count)
            $contractsBySupplier = Contract::selectRaw('supplier_id, count(*) as count')
                ->groupBy('supplier_id')
                ->with('supplier') // Assuming relationship exists
                ->orderByDesc('count')
                ->take(5)
                ->get();
            
            // Base Stats (legacy structure, can be kept or merged)
            $stats = [
                'approvedDocuments' => PaymentDocument::where('status', 'approved')->count(),
                'pendingContracts' => PaymentDocument::where('status', 'approved')->doesntHave('finalPaymentRequests')->count(),
                'totalContracts' => $totalContractsCount,
                'totalContractValue' => $totalContractsValue,
                'activeContracts' => $activeContractsCount,
                'expiringContracts' => $expiringContractsCount,
                'pendingFinalRequests' => FinalPaymentRequest::where('status', 'submitted_to_finance')->count(),
                'totalFinalRequests' => FinalPaymentRequest::count(),
            ];

            return view('dashboards.commercial', compact(
                'approvedDocs', 
                'recentContracts', 
                'myFinalRequests', 
                'stats',
                'contractsByStatus',
                'contractsBySupplier',
                'expiringContracts'
            ));
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
