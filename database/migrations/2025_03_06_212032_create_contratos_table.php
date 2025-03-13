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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_contratante_id');
            $table->string('nro_contrato');
            $table->decimal('mant_prev_usd', 20, 2)->default(0.00);
            $table->decimal('mant_correc_usd', 20, 2)->default(0.00);
            $table->decimal('monto_total_usd', 20, 2)->default(0.00);
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};