<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Module Access Permissions
            'users.access', 'suppliers.access', 'projects.access', 'banks.access', 'teams.access',
            
            // CRUD Permissions
            'users.index', 'users.create', 'users.update', 'users.delete',
            'suppliers.index', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'projects.index', 'projects.create', 'projects.update', 'projects.delete',
            'banks.index', 'banks.create', 'banks.update', 'banks.delete',
            'teams.index', 'teams.create', 'teams.update', 'teams.delete',
            
            // Payment Document & Workflow Permissions
            'payment_documents.create',
            'payment_documents.view_own',
            'payment_documents.view_all',
            'payment_documents.review',  // Procurement Reviewer
            'payment_documents.approve', // Procurement Reviewer
            'payment_documents.reject',  // Procurement Reviewer
            
            'contracts.create',
            'contracts.view_all',
            
            'final_payment_requests.create',
            'final_payment_requests.view_own',
            'final_payment_requests.view_all',
            'final_payment_requests.approve_finance',
            'final_payment_requests.mark_paid',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['guard_name' => 'web']);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['guard_name' => 'web']);
        
        $procurementRole = Role::firstOrCreate(['name' => 'procurement'], ['guard_name' => 'web']);
        $procurementReviewerRole = Role::firstOrCreate(['name' => 'procurement_reviewer'], ['guard_name' => 'web']);
        $financeRole = Role::firstOrCreate(['name' => 'finance'], ['guard_name' => 'web']);
        $commercialRole = Role::firstOrCreate(['name' => 'commercial'], ['guard_name' => 'web']);
        $financeApproverRole = Role::firstOrCreate(['name' => 'finance_approver'], ['guard_name' => 'web']);
        $financeChequeRole = Role::firstOrCreate(['name' => 'finance_cheque_prepare'], ['guard_name' => 'web']);

        // Assign Permissions
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id')->all());

        // User Role (Basic access)
        $userPerms = Permission::where('name', 'like', 'projects.%')->orWhere('name', 'like', 'suppliers.%')->get();
        $userRole->permissions()->sync($userPerms->pluck('id')->all());

        // Procurement Officer: Create documents, view own
        $procurementPerms = Permission::whereIn('name', [
            'suppliers.access', 'suppliers.index', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'teams.access', 'teams.index',
            'payment_documents.create', 'payment_documents.view_own',
        ])->get();
        $procurementRole->permissions()->sync($procurementPerms->pluck('id')->all());
        
        // Procurement Reviewer: Review documents
        $procurementReviewerPerms = Permission::whereIn('name', [
            'payment_documents.view_all', 
            'payment_documents.review', 
            'payment_documents.approve', 
            'payment_documents.reject',
            'suppliers.index', 'projects.index'
        ])->get();
        $procurementReviewerRole->permissions()->sync($procurementReviewerPerms->pluck('id')->all());

        // Commercial: Create contracts, Final requests
        $commercialPerms = Permission::whereIn('name', [
            'payment_documents.view_all',
            'contracts.create', 'contracts.view_all',
            'final_payment_requests.create', 'final_payment_requests.view_own',
            'suppliers.index', 'projects.index'
        ])->get();
        $commercialRole->permissions()->sync($commercialPerms->pluck('id')->all());

        // Finance: Approve final requests
        $financePerms = Permission::whereIn('name', [
            'final_payment_requests.view_all', 'final_payment_requests.approve_finance',
            'suppliers.index', 'projects.index', 'contracts.view_all'
        ])->get();
        $financeRole->permissions()->sync($financePerms->pluck('id')->all());
        $financeApproverRole->permissions()->sync($financePerms->pluck('id')->all());

        // Finance Cheque Prepare: Mark as Paid
        $chequePerms = Permission::whereIn('name', [
            'final_payment_requests.view_all', 'final_payment_requests.mark_paid',
            'suppliers.index', 'projects.index'
        ])->get();
        $financeChequeRole->permissions()->sync($chequePerms->pluck('id')->all());

        // Attach admin role to first user (if exists)
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }
    }
}
