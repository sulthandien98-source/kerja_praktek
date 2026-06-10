<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('status');
            }

            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', [
                    'unpaid',
                    'uploaded',
                    'approved',
                    'rejected'
                ])->default('unpaid')->after('payment_proof');
            }

            if (!Schema::hasColumn('orders', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'payment_uploaded_at')) {
                $table->timestamp('payment_uploaded_at')
                    ->nullable()
                    ->after('payment_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (Schema::hasColumn('orders', 'payment_note')) {
                $table->dropColumn('payment_note');
            }

            if (Schema::hasColumn('orders', 'payment_uploaded_at')) {
                $table->dropColumn('payment_uploaded_at');
            }
        });
    }
};