<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Valuacion extends Model
{
    protected $table = 'valuacions';

    protected $fillable = [
        'codigo',
        'descripcion',
        'empresa_contratante_id',
        'contrato_id',
        'nro_contrato',
        'contrato_id',
        'doc_pdf',
        'monto_usd',
        'monto_bsd',
        'tasa_bcv',
        'mantenimiento_id',
        'responsable'
        
    ];

    /**
     * Get the user associated with the Valuacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mantenimiento(): HasOne
    {
        return $this->hasOne(Mantenimiento::class, 'id', 'mantenimiento_id');
    }

    /**
     * Get the user associated with the Valuacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function empresaContratante(): HasOne
    {
        return $this->hasOne(EmpresaContratante::class, 'id', 'empresa_contratante_id');
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