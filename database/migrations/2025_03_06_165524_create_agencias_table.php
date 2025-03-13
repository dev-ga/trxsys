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
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('estado_id');
            $table->string('ci_rif')->nullable();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono_local')->nullable();
            $table->string('telefono_cel')->nullable();
            $table->string('email')->unique();
            $table->string('responsable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencias');
    }
};