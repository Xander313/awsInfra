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
        Schema::table('iam.app_user', function (Blueprint $table) {
            // Columna simple de texto, nullable por si hay usuarios antiguos
            $table->string('provincia', 100)->nullable()->after('full_name');
            $table->string('canton', 100)->nullable()->after('provincia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iam.app_user', function (Blueprint $table) {
            $table->dropColumn('provincia');
            $table->dropColumn('canton');
        });
    }
};
