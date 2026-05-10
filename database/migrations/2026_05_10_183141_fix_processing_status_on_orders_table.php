<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'proccessing')
            ->update([
                'status' => 'processing'
            ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'waiting',
                'processing',
                'completed',
                'cancelled'
            ])->change();
        });
    }

    public function down(): void
    {

    /**
     * Reverse the migrations.
     */
        DB::table('orders')
            ->where('status', 'processing')
            ->update([
                'status' => 'proccessing'
            ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'waiting',
                'proccessing',
                'completed',
                'cancelled'
            ])->change();
        });
    }
};
