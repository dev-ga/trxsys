<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ValuacionPreventivo extends Model
{
    protected $table = 'valuacion_preventivos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'empresa_contratante_id',
        'nro_contrato',
        'doc_pdf',
        'monto_usd',
        'monto_bsd',
        'tasa_bcv',
        'responsable'

    ];

    /**
     * Get the user associated with the Valuacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function empresaContratante(): HasOne
    {
        return $this->hasOne(EmpresaContratante::class, 'id', 'empresa_contratante_id');
    }
}