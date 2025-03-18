<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bitacora extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_contratante_id',
        'contrato_id',
        'nro_contrato',
        'image',
        'doc_pdf',
        'trabajo_realizado',        
        'mantenimiento_id',
        'valuacion_id',
        'responsable',
        'agencia_id',
        'nro_presupuesto',
        'monto_presupuesto_usd'

    ];

    /**
     * Get the user that owns the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agencia(): BelongsTo
    {
        return $this->belongsTo(Agencia::class, 'agencia_id', 'id');
    }

    /**
     * Get the user associated with the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function empresaContratante(): HasOne
    {
        return $this->hasOne(EmpresaContratante::class, 'id', 'empresa_contratante_id');
    }

    /**
     * Get the user associated with the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mantenimiento(): HasOne
    {
        return $this->hasOne(Mantenimiento::class, 'id', 'mantenimiento_id');
    }

    /**
     * Get the user associated with the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function valuacion(): HasOne
    {
        return $this->hasOne(Valuacion::class, 'id', 'valuacion_id');
    }

    /**
     * Get the user that owns the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }
}