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
        Schema::create('iam.user_role_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('iam.app_user', 'user_id');
            $table->foreignId('role_id')->nullable()->constrained('iam.role', 'role_id');
            $table->string('action'); // 'assigned' | 'removed'
            $table->foreignId('assigned_by')->nullable()->constrained('iam.app_user', 'user_id');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iam.user_role_history');
    }
};
