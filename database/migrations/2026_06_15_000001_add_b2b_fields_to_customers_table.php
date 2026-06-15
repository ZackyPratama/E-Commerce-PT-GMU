<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('type', ['b2c', 'b2b'])->default('b2c')->after('is_active');
            $table->string('company_name')->nullable()->after('type');
            $table->string('company_registration_number')->nullable()->after('company_name');
            $table->enum('b2b_status', ['pending', 'approved', 'rejected'])->nullable()->after('company_registration_number');
            $table->timestamp('approved_at')->nullable()->after('b2b_status');
            $table->string('rejection_reason')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'company_name',
                'company_registration_number',
                'b2b_status',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};
