<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products: packaging type + active flag
        Schema::table('products', function (Blueprint $table) {
            $table->string('packaging_type', 20)->default('zak')->after('unit');
            $table->boolean('is_active')->default(true)->after('min_stock');
        });

        // Categories: active flag
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['packaging_type', 'is_active']);
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
