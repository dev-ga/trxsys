<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioMovimientoController extends Controller
{
    /**
     * Reposicion de inventario
     * ------------------------
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     * 
     */
    static function reposicion($data, $record, $tipo_movimiento)
    {

        try {

            $record->cantidad = $record->cantidad + $data['cantidad'];
            $record->save();

            //cargamos el movimiento de inventario en la tabla inventario_movimientos
            $movimiento = new \App\Models\InventarioMovimiento();
            $movimiento->inventario_id      = $record->id;
            $movimiento->articulo_id        = $record->articulo_id;
            $movimiento->almacen_id         = $record->almacen_id;
            $movimiento->tipo_movimiento    = $tipo_movimiento;
            $movimiento->cantidad           = $data['cantidad'];
            $movimiento->responsable        = Auth::user()->name;
            $movimiento->codigo_articulo    = $record->codigo;
            $movimiento->nro_factura        = $data['nro_factura'] == '' ? 'N/A' : $data['nro_factura'];

            $movimiento->save();

            return true;
            
        } catch (\Throwable $th) {
            
            dd($th);
            return $th;
        }
    }
    
}