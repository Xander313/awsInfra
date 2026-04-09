<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS risk');

        Schema::create('risk.incident_document', function (Blueprint $table) {
            $table->bigIncrements('incident_doc_id');
            $table->unsignedBigInteger('incident_id');
            $table->unsignedBigInteger('doc_ver_id');
            $table->string('relation_type', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('attached_at')->useCurrent();

            $table->unique(['incident_id', 'doc_ver_id'], 'risk_incident_document_unique');
            $table->index(['doc_ver_id', 'attached_at'], 'risk_incident_document_doc_attached_idx');

            $table->foreign('incident_id', 'risk_incident_document_incident_fk')
                ->references('incident_id')
                ->on('risk.incident')
                ->cascadeOnDelete();

            $table->foreign('doc_ver_id', 'risk_incident_document_doc_ver_fk')
                ->references('doc_ver_id')
                ->on('privacy.document_version')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk.incident_document');
    }
};
