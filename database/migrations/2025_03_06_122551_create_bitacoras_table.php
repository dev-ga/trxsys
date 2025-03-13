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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->integer('agencia_id');
            $table->integer('empresa_contratante_id');
            $table->string('nro_contrato');
            $table->string('image');
            $table->string('doc_pdf');
            $table->string('trabajo_realizado');
            $table->string('mantenimiento_id');
            $table->string('valuacion_id');
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};