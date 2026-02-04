<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iam.role', function (Blueprint $table) {
            // Agregamos columna status con valor por defecto 'activo'
            $table->string('status', 50)->default('activo')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('iam.role', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};