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
        Schema::table('expense_categories', function($table)
        {
            $table->string('description', 255)->nullable()->change();
        });
        Schema::table('expenses', function($table)
        {
            $table->string('description', 255)->nullable()->change();
        });
        Schema::table('incomes', function($table)
        {
            $table->string('description', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function($table)
        {
            $table->string('description', 255)->nullable(false)->change();
        });
        Schema::table('expenses', function($table)
        {
            $table->string('description', 255)->nullable(false)->change();
        });
        Schema::table('incomes', function($table)
        {
            $table->string('description', 255)->nullable(false)->change();
        });
    }
};
