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
        Schema::create('empresa_contratantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('ci_rif');
            $table->string('nro_contrato');
            $table->decimal('monto_total_usd', 8, 2)->default(0.00);
            $table->decimal('monto_total_bsd', 8, 2)->default(0.00);
            $table->decimal('tasa_bcv', 8, 2)->default(0.00);
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_contratantes');
    }
};