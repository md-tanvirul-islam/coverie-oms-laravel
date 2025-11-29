<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courier_paid_invoices', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->nullable()->index();

            $table->string('merchant_order_id')->nullable();
            $table->string('courier_name')->index();
            $table->string('consignment_id')->index();
    
            $table->dateTime('created_date')->nullable();

            $table->string('invoice_type')->nullable();

            $table->decimal('collected_amount', 10, 2)->default(0);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();

            $table->decimal('collectable_amount', 10, 2)->default(0);
            $table->decimal('cod_fee', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('final_fee', 10, 2)->default(0);

            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('additional_charge', 10, 2)->default(0);
            $table->decimal('compensation_cost', 10, 2)->default(0);
            $table->decimal('promo_discount', 10, 2)->default(0);
            $table->decimal('payout', 10, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['courier_name', 'consignment_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_paid_invoices');
    }
};
