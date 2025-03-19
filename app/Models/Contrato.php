<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'denominacion',
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

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mantenimientoPreventivos(): HasMany
    {
        return $this->hasMany(MantenimientoPreventivo::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mantenimientoCorrectivos(): HasMany
    {
        return $this->hasMany(MantenimientoCorrectivo::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function agencias(): HasMany
    {
        return $this->hasMany(Agencia::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function valuaciones(): HasMany
    {
        return $this->hasMany(Valuacion::class, 'contrato_id', 'id');
    }

    /**
     * Get all of the comments for the Contrato
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class, 'contrato_id', 'id');
    }
}