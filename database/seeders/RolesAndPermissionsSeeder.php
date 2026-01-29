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
            
            // Payment Request Permissions
            'payment_requests.create',
            'payment_requests.view_own',
            'payment_requests.view_all',
            'payment_requests.approve_commercial',
            'payment_requests.approve_finance',
            'payment_requests.approve_payment', // For finance_cheque_prepare (mark as paid)
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm], ['guard_name' => 'web']);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['guard_name' => 'web']);
        
        $procurementRole = Role::firstOrCreate(['name' => 'procurement'], ['guard_name' => 'web']);
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

        // Procurement: Create requests, view own, access to suppliers and teams
        $procurementPerms = Permission::whereIn('name', [
            'suppliers.access', 'suppliers.index', 'suppliers.create', 'suppliers.update', 'suppliers.delete',
            'teams.access', 'teams.index',
            'payment_requests.create', 'payment_requests.view_own',
        ])->get();
        $procurementRole->permissions()->sync($procurementPerms->pluck('id')->all());

        // Commercial: View submitted, Approve Commercial, View All
        $commercialPerms = Permission::whereIn('name', [
            'payment_requests.view_all', 'payment_requests.approve_commercial',
            'suppliers.index', 'projects.index'
        ])->get();
        $commercialRole->permissions()->sync($commercialPerms->pluck('id')->all());

        // Finance: View approved commercial, Approve Finance, View All
        $financePerms = Permission::whereIn('name', [
            'payment_requests.view_all', 'payment_requests.approve_finance',
            'suppliers.index', 'projects.index'
        ])->get();
        $financeRole->permissions()->sync($financePerms->pluck('id')->all());
        
        // Finance Approver: Same as Finance but maybe higher level (treating same as finance for now based on description, or just approve_finance)
        $financeApproverRole->permissions()->sync($financePerms->pluck('id')->all());

        // Finance Cheque Prepare: View approved finance, Mark as Paid
        $chequePerms = Permission::whereIn('name', [
            'payment_requests.view_all', 'payment_requests.approve_payment',
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
