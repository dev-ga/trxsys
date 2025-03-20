<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipo extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'equipos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agencia_id',
        'contrato_id',
        
        'toneladas',
        'toneladas_eva',

        'ph',
        'ph_eva',

        'refrigerante',
        'refrigerante_eva',

        'area_suministro',
        
        'voltaje',
        'voltaje_eva',

        'responsable',
        'codigo',
        'image_placa_condensadora',
        'image_placa_ventilador',
        
        'motor_ventilador_hp',
        'motor_ventilador_hp_eva',

        'motor_ventilador_eje',
        'motor_ventilador_eje_eva',

        'rpm',
        'rpm_eva',

        'tipo_correa',
        'tipo_correa_eva',

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
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function MantenimientoPreventivos(): HasMany
    {
        return $this->hasMany(MantenimientoPreventivo::class, 'equipo_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function MantenimientoCorrectivos(): HasMany
    {
        return $this->hasMany(MantenimientoCorrectivo::class, 'equipo_id', 'id');
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