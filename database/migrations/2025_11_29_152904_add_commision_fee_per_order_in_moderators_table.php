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
        Schema::table('moderators', function (Blueprint $table) {
            $table->decimal('commission_fee_per_order', 10, 2)->default(0)->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moderators', function (Blueprint $table) {
            $table->dropColumn('commission_fee_per_order');
        });
    }
};
