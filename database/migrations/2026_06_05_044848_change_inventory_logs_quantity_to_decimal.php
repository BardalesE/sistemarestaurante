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
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->decimal('quantity', 10, 3)->change();
            $table->decimal('old_stock', 10, 3)->nullable()->change();
            $table->decimal('new_stock', 10, 3)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('old_stock')->nullable()->change();
            $table->integer('new_stock')->nullable()->change();
        });
    }
};
