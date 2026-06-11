<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $warehouseAdmin = Role::create(['name' => 'WarehouseAdmin']);
        $operator = Role::create(['name' => 'Operator']);

        // Product permissions
        $productPermissions = [
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
        ];

        foreach ($productPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Inventory permissions
        $inventoryPermissions = [
            'view-inventory',
            'create-inventory',
            'edit-inventory',
            'delete-inventory',
        ];

        foreach ($inventoryPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // User management permissions
        $userPermissions = [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
        ];

        foreach ($userPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Task Center permissions
        $taskPermissions = [
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
        ];

        foreach ($taskPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Stock Opname permissions
        $stockOpnamePermissions = [
            'view-stock-opnames',
            'create-stock-opnames',
            'edit-stock-opnames',
            'delete-stock-opnames',
        ];

        foreach ($stockOpnamePermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Approval permissions
        $approvalPermissions = [
            'view-approvals',
            'create-approvals',
            'edit-approvals',
            'delete-approvals',
        ];

        foreach ($approvalPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Dashboard permissions
        $dashboardPermissions = [
            'access-admin-dashboard',
        ];

        foreach ($dashboardPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // SuperAdmin: full access
        $superAdmin->givePermissionTo(Permission::all());

        // WarehouseAdmin: operational access (NOT user management)
        $warehouseAdmin->givePermissionTo([
            // Products
            'view-products',
            'create-products',
            'edit-products',
            // Inventory
            'view-inventory',
            'create-inventory',
            'edit-inventory',
            // Tasks
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            // Stock Opnames
            'view-stock-opnames',
            'create-stock-opnames',
            'edit-stock-opnames',
            // Approvals - can approve
            'view-approvals',
            'create-approvals',
            'edit-approvals',
            // Dashboard
            'access-admin-dashboard',
        ]);

        // Operator: limited access
        $operator->givePermissionTo([
            // Products
            'view-products',
            // Inventory
            'view-inventory',
            // Tasks
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            // Stock Opnames - can view and count
            'view-stock-opnames',
            'edit-stock-opnames',
            // Approvals - can only create requests
            'view-approvals',
            'create-approvals',
        ]);
    }
}