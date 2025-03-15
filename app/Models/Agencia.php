<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci_rif',
        'estado_id',
        'codigo',
        'nombre',
        'direccion',
        'telefono_local',
        'telefono_celular',
        'email',
        'responsable',
        'empresa_contratante_id',
    ];
    
    //relacion hasMany con la tabla de estados
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Get the Servicio that owns the Sucursal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class, 'id', 'agencia_id');
    }

    /**
     * Get the Servicio that owns the empresa contratante
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function EmpresaContratante(): BelongsTo
    {
        return $this->belongsTo(EmpresaContratante::class, 'empresa_contratante_id', 'id');
    }


    public function detalle_gasto(): BelongsTo
    {
        return $this->belongsTo(GastoDetalle::class, 'gasto_detalle_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'agencia_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'agencia_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function MantenimientoPreventivos(): HasMany
    {
        return $this->hasMany(MantenimientoPreventivo::class, 'agencia_id', 'id');
    }

    /**
     * Get all of the comments for the Agencia
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function MantenimientoCorrectivos(): HasMany
    {
        return $this->hasMany(MantenimientoCorrectivo::class, 'agencia_id', 'id');
    }
}