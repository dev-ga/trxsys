<?php

namespace App\Http\Controllers;

use App\Models\GastoDetalle;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class GastoDetalleController extends Controller
{
    static function crear_detalle($data, $record) {

        try {

            //DETALLES EN DOLARES USD
            if($data['agencias'][0]['monto_usd'] != null) {

                //Restriccion para validar si ya la factura tiene detalles cargados
                $prev_detalle = GastoDetalle::where('gasto_id', $record->id)->get('monto_usd')->sum('monto_usd');
                // dd($prev_detalle);
                if ($prev_detalle == $record->monto_usd) {
                    
                    return $res = [
                        'success' => false,
                        'message' => 'La factura ya tiene detalles cargados. No puede agregar otro movimiento ya que la suma de los montos asociados es mayor al monto de la factura.'
                    ];
                    
                }else{

                    //Restriccion para que la suma de los montos asociados a la factura sea igual a la factura padre
                    $montos = [];
                    for ($i = 0; $i < count($data['agencias']); $i++) {
                        array_push($montos, $data['agencias'][$i]['monto_usd']);
                    }

                    $total_detalle = array_sum($montos);
                    if ($total_detalle != $record->monto_usd) {

                        return $res = [
                            'success' => false,
                            'message' => 'La suma de los montos asociados no es igual al monto total de la factura.'
                        ];
                    }

                    if ($total_detalle == $record->monto_usd) {

                        for ($i = 0; $i < count($data['agencias']); $i++) {
                            $detalle = new GastoDetalle;
                            $detalle->gasto_id                  = $record->id;
                            $detalle->codigo_gasto              = $record->codigo;
                            $detalle->empresa_contratante_id    = $record->empresa_contratante_id;
                            $detalle->nro_contrato              = $data['nro_contrato'];
                            $detalle->agencia_id                = $data['agencias'][$i]['agencia_id'];
                            $detalle->monto_usd                 = $data['agencias'][$i]['monto_usd'];
                            $detalle->tasa_bcv                  = Configuracion::first()->tasa_bcv;
                            $detalle->responsable               = Auth::user()->name;
                            $detalle->save();
                        }

                        return $res = [
                            'success' => true,
                            'message' => 'Detalle creado exitosamente'
                        ];
                    }   
                    
                }   
                
            }

            //DETALLES EN BOLIVARES BSD
            if ($data['agencias'][0]['monto_bsd'] != null) {

                //Restriccion para validar si ya la factura tiene detalles cargados
                $prev_detalle = GastoDetalle::where('gasto_id', $record->id)->get('monto_bsd')->sum('monto_bsd');

                if ($prev_detalle == $record->monto_bsd) {

                    return $res = [
                        'success' => false,
                        'message' => 'La factura ya tiene detalles cargados. No puede agregar otro movimiento ya que la suma de los montos asociados es mayor al monto de la factura.'
                    ];
                } else {

                    //Restriccion para que la suma de los montos asociados a la factura sea igual a la factura padre
                    $montos = [];
                    for ($i = 0; $i < count($data['agencias']); $i++) {
                        array_push($montos, $data['agencias'][$i]['monto_bsd']);
                    }

                    $total_detalle = array_sum($montos);
                    if ($total_detalle != $record->monto_bsd) {

                        return $res = [
                            'success' => false,
                            'message' => 'La suma de los montos asociados no es igual al monto total de la factura.'
                        ];
                    }

                    if ($total_detalle == $record->monto_bsd) {

                        for ($i = 0; $i < count($data['agencias']); $i++) {
                            $detalle = new GastoDetalle;
                            $detalle->gasto_id                  = $record->id;
                            $detalle->codigo_gasto              = $record->codigo;
                            $detalle->empresa_contratante_id    = $record->empresa_contratante_id;
                            $detalle->nro_contrato              = $data['nro_contrato'];
                            $detalle->agencia_id                = $data['agencias'][$i]['agencia_id'];
                            $detalle->monto_bsd                 = $data['agencias'][$i]['monto_bsd'];
                            $detalle->tasa_bcv                  = Configuracion::first()->tasa_bcv;
                            $detalle->responsable               = Auth::user()->name;
                            $detalle->save();
                        }

                        return $res = [
                            'success' => true,
                            'message' => 'Detalle creado exitosamente'
                        ];
                    }
                }

                
            }
            
        } catch (\Throwable $th) {
            return $res = [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }
        
    }
}