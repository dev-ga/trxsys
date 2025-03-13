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

            //Restriccion para validar si ya la factura tiene detalles cargados
            $prev_detalle = GastoDetalle::where('gasto_id', $record->id)->get('monto_usd')->sum('monto_usd');
            if ($prev_detalle == $record->monto_usd) {
                Notification::make()
                ->title('Notificacion')
                ->color('warning')
                ->icon('heroicon-o-shield-check')
                ->iconColor('warning')
                ->body('La factura ya tiene detalles cargados. No puede agregar otro movimiento ya que la suma de los montos asociados es mayor al monto de la factura.')
                ->send();
                
                return false;
            }


            //Restriccion para que la suma de los montos asociados a la factura sea igual a la factura padre
            $montos = [];
            for ($i = 0; $i < count($data['agencias']); $i++) {
                array_push($montos, $data['agencias'][$i]['monto_usd']);
            }

            $total_detalle = array_sum($montos);
            if ($total_detalle != $record->monto_usd) {
                Notification::make()
                ->title('Notificacion')
                ->color('warning')
                ->icon('heroicon-o-shield-check')
                ->iconColor('warning')
                ->body('El monto del detalle debe ser igual a monto de la Factura. Por favor verifique el monto del detalle y vuelva a intentar.')
                ->send();
                
                return false;
            }

            if ($total_detalle == $record->monto_usd) {
                for ($i = 0; $i < count($data['agencias']); $i++) {
                    $detalle = new GastoDetalle;
                    $detalle->gasto_id                  = $record->id;
                    $detalle->codigo_gasto              = $record->codigo;
                    $detalle->empresa_contratante_id    = $record->empresa_contratante_id;
                    $detalle->nro_contrato              = $record->nro_contrato;
                    $detalle->agencia_id                = $data['agencias'][$i]['agencia_id'];
                    $detalle->monto_usd                 = $data['agencias'][$i]['monto_usd'];
                    $detalle->monto_bsd                 = $data['agencias'][$i]['monto_bsd'];
                    $detalle->tasa_bcv                  = Configuracion::first()->tasa_bcv;
                    $detalle->responsable               = Auth::user()->name;
                    $detalle->save();
                }
    
                return true;
                
            }
            
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Notificacion')
                ->color('danger')
                ->icon('heroicon-o-shield-check')
                ->iconColor('danger')
                ->body($th->getMessage())
                ->send();
        }
        
    }
}