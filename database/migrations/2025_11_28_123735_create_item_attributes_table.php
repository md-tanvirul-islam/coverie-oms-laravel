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
        Schema::create('item_attributes', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('item_id');

            $table->string('label');            // Phone Model, Vendor Name
            $table->string('type');             // text, number, select, date
            $table->json('options')->nullable(); // for select (["iPhone","Samsung"])
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_attributes');
    }
};
