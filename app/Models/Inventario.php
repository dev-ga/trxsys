<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'inventarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'articulo_id',
        'almacen_id',
        'cantidad',
        'responsable',
    ];

    /**
     * Get the user associated with the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function articulo(): HasOne
    {
        return $this->hasOne(Articulo::class, 'id', 'articulo_id');
    }

    /**
     * Get the user associated with the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function almacen(): HasOne
    {
        return $this->hasOne(Almacen::class, 'id', 'articulo_id');
    }

    /**
     * Get all of the comments for the Inventario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class, 'inventario_id', 'id');
    }
}