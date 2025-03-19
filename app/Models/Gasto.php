<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gasto extends Model
{
    use HasFactory;

    /**
     * Define table
     */
    protected $table = 'gastos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'descripcion',
        'monto_usd',
        'monto_bsd',
        'forma_pago',
        'fecha_factura',
        'responsable',
        'nro_factura',
        'proveedor_id',
        'fecha',
        'metodo_pago_id',
        'valuacion_id',
        'observaciones',
        'tasa_bcv',
        'exento',
        'almacen_id',
        'total_gasto_bsd',
        'iva',
        'conversion_a_usd',
        'nro_control',
        'created_at',
        'tipo_gasto_id',
        'empresa_contratante_id',
        'contrato_id',
    ];

    /**
     * Get the compra that owns the Proveedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function empresaContratante(): HasOne
    {
        return $this->hasOne(EmpresaContratante::class, 'id', 'empresa_contratante_id');
    }

    /**
     * Get all of the proveedores for the Compra
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function proveedor(): HasOne
    {
        return $this->hasOne(Proveedor::class, 'id', 'proveedor_id');
    }

    /**
     * Get all of the proveedores for the Compra
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function metodo_pago(): HasOne
    {
        return $this->hasOne(MetodoPago::class, 'id', 'metodo_pago_id');
    }

    /**
     * Get all of the tipo gasdo for the tipo gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tipo_gasto(): HasOne
    {
        return $this->hasOne(TipoGasto::class, 'id', 'tipo_gasto_id');
    }

    /**
     * Get all of the tipo gasdo for the tipo gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function valuacion(): HasOne
    {
        return $this->hasOne(Valuacion::class, 'id', 'valuacion_id');
    }

    /**
     * Get all of the detalle_gasto for the Gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalleGastos(): HasMany
    {
        return $this->hasMany(GastoDetalle::class, 'gasto_id', 'id');
    }

    /**
     * Get the user that owns the Gasto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id', 'id');
    }

    
}