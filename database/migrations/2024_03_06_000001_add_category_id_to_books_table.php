<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->after('available_quantity')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

                $table->foreignId('publisher_id')
                ->after('category_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

                $table->foreignId('author_id')
                ->after('publisher_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};