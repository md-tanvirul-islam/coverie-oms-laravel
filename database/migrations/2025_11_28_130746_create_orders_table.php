<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('taker_employee_id')->nullable();

            $table->string('invoice_code');
            $table->date('order_date');

            // Customer info
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();

            // Calculated fields
            $table->unsignedBigInteger('total_quantity')->default(1);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2);

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Indexes (important)
            $table->unique(['store_id', 'invoice_code']);
            $table->index(['store_id', 'order_date']);
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
