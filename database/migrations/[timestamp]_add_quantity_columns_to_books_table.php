<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'quantity')) {
                $table->integer('quantity')->default(1);
            }
            if (!Schema::hasColumn('books', 'available_quantity')) {
                $table->integer('available_quantity')->default(1);
            }
            if (!Schema::hasColumn('books', 'status')) {
                $table->string('status')->default('available');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'available_quantity', 'status']);
        });
    }
};