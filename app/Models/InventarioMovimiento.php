<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventarioMovimiento extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'inventario_movimientos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventario_id',
        'articulo_id',
        'almacen_id',
        'tipo_movimiento',
        'cantidad',
        'responsable',
        'codigo_articulo',
        'nro_factura',
    ];

    /**
     * Get the user that owns the InventarioMovimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Articulo::class, 'articulo_id', 'id');
    }

    /**
     * Get the user that owns the InventarioMovimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario(): BelongsTo
    {
        return $this->belongsTo(Inventario::class, 'id', 'inventario_id');
    }

    /**
     * Get the user associated with the InventarioMovimiento
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function almacen(): HasOne
    {
        return $this->hasOne(Almacen::class, 'id', 'almacen_id');
    }
}