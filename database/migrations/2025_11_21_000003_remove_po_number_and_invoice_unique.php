<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('purchase_orders', 'po_number')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('po_number');
            });
        }

        if (! Schema::hasIndex('invoices', 'invoices_customer_id_period_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['customer_id', 'period']);
            });
        }

        if (Schema::hasIndex('invoices', 'invoices_customer_id_period_unique')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropUnique(['customer_id', 'period']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasIndex('invoices', 'invoices_customer_id_period_unique')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unique(['customer_id', 'period']);
            });
        }

        if (Schema::hasIndex('invoices', 'invoices_customer_id_period_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex(['customer_id', 'period']);
            });
        }

        if (! Schema::hasColumn('purchase_orders', 'po_number')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('po_number')->nullable()->after('customer_id');
            });
        }
    }
};
