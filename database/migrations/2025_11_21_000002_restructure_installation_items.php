<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('installation_done_at');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('include_installation');
        });

        Schema::dropIfExists('installation_items');

        Schema::create('installation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('po_id');
            $table->string('name');
            $table->decimal('qty', 10, 2)->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->index('po_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_items');

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->boolean('include_installation')->default(false);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('installation_done_at')->nullable();
        });

        Schema::create('installation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('modified_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
