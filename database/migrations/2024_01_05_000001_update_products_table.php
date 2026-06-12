<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('name');
            }
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 20)->default('PCS')->after('supplier_id');
            }
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0)->after('unit');
            }
            if (!Schema::hasColumn('products', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->default(0)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(0)->after('selling_price');
            }
            if (!Schema::hasColumn('products', 'max_stock')) {
                $table->integer('max_stock')->default(0)->after('min_stock');
            }
            if (!Schema::hasColumn('products', 'current_stock')) {
                $table->integer('current_stock')->default(0)->after('max_stock');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('current_stock');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode', 50)->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }

            $table->index('category_id', 'products_category_id_index');
            $table->index('supplier_id', 'products_supplier_id_index');
            $table->index('barcode', 'products_barcode_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'category_id', 'supplier_id', 'unit', 'cost_price',
                'selling_price', 'min_stock', 'max_stock', 'current_stock',
                'description', 'barcode', 'image', 'is_active',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};