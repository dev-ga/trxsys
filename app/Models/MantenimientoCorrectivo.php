<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MantenimientoCorrectivo extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'mantenimiento_correctivos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'valuacion_id',
        'contrato_id',
        'agencia_id',
        'equipo_id',
        'codigo_equipo',
        'nro_presupuesto',
        'monto_presupuesto_usd',
        'detalles',
        'doc_pdf',
        'responsable',
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
     * Get the user that owns the Bitacora
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
    }

    /**
     * Get the user associated with the MantenimientoCorrectivo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function valuacionCorrectivo(): HasOne
    {
        return $this->hasOne(ValuacionCorrectivo::class, 'id', 'valuacion_correctivo_id');
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