<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci_rif',
        'codigo',
        'nombre',
        'direccion',
        'telefono_local',
        'telefono_celular',
        'email',
        'responsable',
    ];

    /**
     * Get the gastos that owns the Proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

}