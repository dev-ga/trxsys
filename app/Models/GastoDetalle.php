<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoDetalle extends Model
{
    protected $table = 'gasto_detalles';

    protected $fillable = [
        'gasto_id',
        'codigo_gasto',
        'empresa_contratante_id',
        'nro_contrato',
        'agencia_id',
        'valuacion_id',
        'monto_usd',
        'monto_bsd',
        'tasa_bcv',
        'responsable',
    ];

    /**
     * Get the user that owns the GastoDetalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class, 'gasto_id', 'id');
    }

    /**
     * Get the user associated with the GastoDetalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function agencia(): HasOne
    {
        return $this->hasOne(Agencia::class, 'id', 'agencia_id');
    }

    /**
     * Get the user associated with the GastoDetalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function valuacion(): HasOne
    {
        return $this->hasOne(Valuacion::class, 'id', 'valuacion_id');
    }

    /**
     * Get the user associated with the GastoDetalle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function empresaContratante(): HasOne
    {
        return $this->hasOne(EmpresaContratante::class, 'id', 'empresa_contratante_id');
    }

    
}