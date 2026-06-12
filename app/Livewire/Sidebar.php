<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public bool $isOpen = false;
    public bool $isCollapsed = false;

    public function toggleSidebar(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function toggleCollapse(): void
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function render()
    {
        return view('livewire.sidebar', [
            'menuItems' => $this->getMenuItems(),
        ]);
    }

    private function getMenuItems(): array
    {
        $items = [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'permission' => 'view-dashboard',
                'children' => [],
            ],
            [
                'label' => 'Master Data',
                'route' => '#',
                'permission' => null,
                'children' => [
                    ['label' => 'Products', 'route' => 'products.index', 'permission' => 'view-products'],
                    ['label' => 'Categories', 'route' => 'categories.index', 'permission' => 'view-categories'],
                    ['label' => 'Suppliers', 'route' => 'suppliers.index', 'permission' => 'view-suppliers'],
                ],
            ],
            [
                'label' => 'Transactions',
                'route' => '#',
                'permission' => null,
                'children' => [
                    ['label' => 'Stock In', 'route' => 'inventory.stock-in.index', 'permission' => 'view-stock-in'],
                    ['label' => 'Stock Out', 'route' => 'inventory.stock-out.index', 'permission' => 'view-stock-out'],
                ],
            ],
            [
                'label' => 'Inventory',
                'route' => '#',
                'permission' => null,
                'children' => [
                    ['label' => 'Stock Ledger', 'route' => 'inventory.ledger', 'permission' => 'view-stock-ledger'],
                    ['label' => 'Adjustments', 'route' => 'inventory.adjustment.index', 'permission' => 'adjust-stock'],
                ],
            ],
            [
                'label' => 'Reports',
                'route' => '#',
                'permission' => null,
                'children' => [
                    ['label' => 'Stock Report', 'route' => 'reports.stock', 'permission' => 'view-stock-report'],
                ],
            ],
            [
                'label' => 'Administration',
                'route' => '#',
                'permission' => null,
                'children' => [
                    ['label' => 'Users', 'route' => 'users.index', 'permission' => 'view-users'],
                    ['label' => 'Roles', 'route' => 'roles.index', 'permission' => 'view-roles'],
                ],
            ],
        ];

        if (auth()->check()) {
            return array_filter($items, function ($item) {
                if ($item['permission']) {
                    return auth()->user()->can($item['permission']);
                }
                if (!empty($item['children'])) {
                    return count(array_filter($item['children'], fn ($child) => auth()->user()->can($child['permission']))) > 0;
                }
                return true;
            });
        }

        return [];
    }
}