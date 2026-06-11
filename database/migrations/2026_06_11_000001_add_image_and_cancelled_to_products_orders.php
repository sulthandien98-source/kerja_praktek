<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            $existing = \DB::select("SHOW COLUMNS FROM orders LIKE 'status'");
            if (!empty($existing)) {
                $col = $existing[0];
                if (strpos($col->Type ?? $col->type ?? '', 'dibatalkan') === false) {
                    $table->string('status')->default('menunggu_konfirmasi')->change();
                }
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};