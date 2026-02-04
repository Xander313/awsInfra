<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iam.permission', function (Blueprint $table) {
            // Estado por defecto 'activo'
            $table->string('status', 50)->default('activo')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('iam.permission', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};