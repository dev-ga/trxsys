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
        Schema::create('valuacions', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->integer('empresa_contratante_id');
            $table->string('nro_contrato');
            $table->string('doc_pdf');
            $table->decimal('monto_usd', 20, 2)->default(0.00);
            $table->decimal('monto_bsd', 20, 2)->default(0.00);
            $table->decimal('tasa_bcv', 8, 2)->default(0.00);
            $table->integer('mantenimiento_id');
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valuacions');
    }
};