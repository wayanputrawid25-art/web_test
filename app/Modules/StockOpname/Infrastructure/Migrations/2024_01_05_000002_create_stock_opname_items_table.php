<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('system_quantity', 12, 2)->default(0);
            $table->decimal('counted_quantity', 12, 2)->nullable();
            $table->decimal('variance', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('counter_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('counted_at')->nullable();
            $table->timestamps();

            $table->index('stock_opname_session_id');
            $table->index('product_id');
            $table->index('counter_id');
            $table->unique(['stock_opname_session_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};