<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Articulo extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'articulos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categoria_id',
        'codigo',
        'descripcion',
        'precio_unitario',
        'responsable'
    ];

    /**
     * Get all of the comments for the Articulo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class, 'articulo_id', 'id');
    }

    /**
     * Get the user associated with the Articulo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function categoria(): HasOne
    {
        return $this->hasOne(Categoria::class, 'id', 'categoria_id');
    }

}