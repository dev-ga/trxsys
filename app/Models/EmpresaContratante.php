<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpresaContratante extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'empresa_contratantes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'ci_rif',
        'nro_contrato',
        'monto_mante_prev_usd',
        'monto_mante_correc_usd',
        'monto_total_usd',
        'monto_total_bsd',
        'tasa_bcv',
        'responsable',
    ];

    /**
     * Get all of the comments for the contratos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'empresa_contratante_id', 'id');
    }

    /**
     * Get all of the comments for the agencias
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agencias(): HasMany
    {
        return $this->hasMany(Agencia::class, 'empresa_contratante_id', 'id');
    }

    

    
}