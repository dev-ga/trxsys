<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    static function entrada($data, $record)
    {

        try {
            
            $entrada = new \App\Models\Inventario();
            $entrada->codigo = $data['codigo'];
            $entrada->cantidad = $data['cantidad'];
            $entrada->articulo_id = $record->id;
            $entrada->responsable = $data['responsable'];
            $entrada->almacen_id = $data['almacen_id'];
            $entrada->nro_factura = $data['nro_factura'] == '' ? null : $data['nro_factura'];
            $entrada->save();

            //cargamos el entrada de inventario en la tabla inventario_movimientos
            $movimiento = new \App\Models\InventarioMovimiento();
            $movimiento->inventario_id      = $entrada->id;
            $movimiento->articulo_id        = $record->id;
            $movimiento->almacen_id         = $data['almacen_id'];
            $movimiento->tipo_movimiento    = 'entrada';
            $movimiento->cantidad           = $data['cantidad'];
            $movimiento->responsable        = $data['responsable'];
            $movimiento->codigo_articulo    = $data['codigo'];
            $movimiento->nro_factura        = $data['nro_factura'] == '' ? 'N/A' : $data['nro_factura'];
            $movimiento->save();

            return true;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    //Reposicion de inventario
    static function reposicion($data, $record){
        
        try {
            
            $record->cantidad = $record->cantidad + $data['cantidad'];
            $record->save();

            //cargamos el movimiento de inventario en la tabla inventario_movimientos
            $movimiento = new \App\Models\InventarioMovimiento();
            $movimiento->inventario_id      = $record->id;     
            $movimiento->articulo_id        = $record->articulo_id;
            $movimiento->almacen_id         = $record->almacen_id;       
            $movimiento->tipo_movimiento    = 'reposicion';
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