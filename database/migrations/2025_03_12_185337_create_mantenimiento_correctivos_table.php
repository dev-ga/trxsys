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
        Schema::create('mantenimiento_correctivos', function (Blueprint $table) {
            $table->id();
            $table->integer('agencia_id');
            $table->integer('equipo_id');
            $table->string('codigo_equipo');
            $table->decimal('toneladas', 8, 2)->default('0.00');
            $table->decimal('calculo_x_tonelada', 8, 2)->default('0.00');
            $table->string('fecha_ejecucion')->nullable();
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_correctivos');
    }
};