<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard' => [
                'view-dashboard',
                'view-admin-dashboard',
            ],
            'users' => [
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
            ],
            'roles' => [
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'assign-roles',
            ],
            'products' => [
                'view-products',
                'create-products',
                'edit-products',
                'delete-products',
                'export-products',
            ],
            'categories' => [
                'view-categories',
                'create-categories',
                'edit-categories',
                'delete-categories',
            ],
            'suppliers' => [
                'view-suppliers',
                'create-suppliers',
                'edit-suppliers',
                'delete-suppliers',
            ],
            'stock_in' => [
                'view-stock-in',
                'create-stock-in',
                'edit-stock-in',
                'delete-stock-in',
                'approve-stock-in',
                'receive-stock-in',
            ],
            'stock_out' => [
                'view-stock-out',
                'create-stock-out',
                'edit-stock-out',
                'delete-stock-out',
                'approve-stock-out',
                'dispatch-stock-out',
            ],
            'inventory' => [
                'view-inventory',
                'manage-inventory',
                'adjust-stock',
                'view-stock-ledger',
            ],
            'reports' => [
                'view-reports',
                'export-reports',
                'view-stock-report',
                'view-movement-report',
                'view-valuation-report',
            ],
            'settings' => [
                'manage-settings',
                'view-activity-log',
            ],
        ];

        foreach ($permissions as $group => $permissionList) {
            foreach ($permissionList as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}