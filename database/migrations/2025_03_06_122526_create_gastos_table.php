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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('descripcion');
            $table->integer('forma_pago');
            $table->integer('metodo_pago');
            $table->string('nro_factura')->unique();
            $table->string('nro_control')->unique();
            $table->integer('sucursal_id');
            $table->integer('proveedor_id');
            $table->decimal('monto_usd', 8, 2)->nullable()->default(0.00);
            $table->decimal('monto_bsd', 8, 2)->nullable()->default(0.00);
            $table->decimal('iva', 8, 2)->nullable()->default(0.00);
            $table->decimal('conversion_a_usd', 8, 2)->nullable()->default(0.00);
            $table->decimal('total_gasto_bsd', 8, 2)->nullable()->default(0.00);
            $table->decimal('tasa_bcv', 8, 2)->nullable()->default(0.00);
            $table->string('observaciones')->nullable();
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};