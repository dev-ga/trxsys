<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contrato extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'contratos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'empresa_contratante_id',
        'nro_contrato',
        'mant_prev_usd',
        'mant_correc_usd',
        'monto_total_usd',
        'responsable',
    ];

    /**
     * Get all of the comments for the contratos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function empresaContratante(): BelongsTo
    {
        return $this->belongsTo(EmpresaContratante::class, 'empresa_contratante_id', 'id');
    }
}