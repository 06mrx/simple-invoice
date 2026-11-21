<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('user_id', 36)->nullable()->after('id');
            $table->dropUnique(['key']);
            $table->unique(['user_id', 'key'], 'settings_user_id_key_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropUnique(['email']);
            $table->unique(['created_by', 'code'], 'customers_created_by_code_unique');
            $table->unique(['created_by', 'email'], 'customers_created_by_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_created_by_email_unique');
            $table->dropUnique('customers_created_by_code_unique');
            $table->unique(['code']);
            $table->unique(['email']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_user_id_key_unique');
            $table->dropColumn('user_id');
            $table->unique(['key']);
        });
    }
};
