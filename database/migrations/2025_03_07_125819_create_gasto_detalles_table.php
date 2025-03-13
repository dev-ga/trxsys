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
        Schema::create('gasto_detalles', function (Blueprint $table) {
            $table->id();
            $table->integer('gasto_id');
            $table->integer('codigo_gasto');
            $table->integer('empresa_contratante_id')->nullable();
            $table->string('nro_contrato')->nullable();
            $table->integer('agencia_id')->nullable();
            $table->decimal('mont_usd', 20, 2)->default(0.00);
            $table->decimal('monto_bsd', 20, 2)->default(0.00);
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto_detalles');
    }
};