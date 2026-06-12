<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $staff = Role::create(['name' => 'Staff', 'guard_name' => 'web']);

        // Dashboard permissions
        $dashboardPermissions = [
            'view-dashboard',
            'view-admin-dashboard',
        ];

        foreach ($dashboardPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // User management permissions
        $userPermissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-roles',
        ];

        foreach ($userPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Product permissions
        $productPermissions = [
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            'export-products',
        ];

        foreach ($productPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Category permissions
        $categoryPermissions = [
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
        ];

        foreach ($categoryPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Supplier permissions
        $supplierPermissions = [
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
        ];

        foreach ($supplierPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Stock In permissions
        $stockInPermissions = [
            'view-stock-in',
            'create-stock-in',
            'edit-stock-in',
            'delete-stock-in',
            'approve-stock-in',
            'receive-stock-in',
        ];

        foreach ($stockInPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Stock Out permissions
        $stockOutPermissions = [
            'view-stock-out',
            'create-stock-out',
            'edit-stock-out',
            'delete-stock-out',
            'approve-stock-out',
            'dispatch-stock-out',
        ];

        foreach ($stockOutPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Inventory permissions
        $inventoryPermissions = [
            'view-inventory',
            'manage-inventory',
            'adjust-stock',
            'view-stock-ledger',
        ];

        foreach ($inventoryPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Report permissions
        $reportPermissions = [
            'view-reports',
            'export-reports',
            'view-stock-report',
            'view-movement-report',
            'view-valuation-report',
        ];

        foreach ($reportPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Settings permissions
        $settingsPermissions = [
            'manage-settings',
            'view-activity-log',
        ];

        foreach ($settingsPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin: full access
        $superAdmin->givePermissionTo(Permission::all());

        // Admin: operational access (NOT user/role management)
        $admin->givePermissionTo([
            // Dashboard
            'view-dashboard',
            'view-admin-dashboard',
            // Products
            'view-products',
            'create-products',
            'edit-products',
            'export-products',
            // Categories
            'view-categories',
            'create-categories',
            'edit-categories',
            // Suppliers
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            // Stock In
            'view-stock-in',
            'create-stock-in',
            'edit-stock-in',
            'approve-stock-in',
            'receive-stock-in',
            // Stock Out
            'view-stock-out',
            'create-stock-out',
            'edit-stock-out',
            'approve-stock-out',
            'dispatch-stock-out',
            // Inventory
            'view-inventory',
            'manage-inventory',
            'adjust-stock',
            'view-stock-ledger',
            // Reports
            'view-reports',
            'export-reports',
            'view-stock-report',
            'view-movement-report',
            'view-valuation-report',
        ]);

        // Staff: limited access
        $staff->givePermissionTo([
            // Dashboard
            'view-dashboard',
            // Products
            'view-products',
            // Categories
            'view-categories',
            // Suppliers
            'view-suppliers',
            // Stock In
            'view-stock-in',
            'create-stock-in',
            // Stock Out
            'view-stock-out',
            'create-stock-out',
            // Inventory
            'view-inventory',
            'view-stock-ledger',
            // Reports
            'view-reports',
            'view-stock-report',
            'view-movement-report',
        ]);
    }
}