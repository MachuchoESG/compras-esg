<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Estatus;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraficosController extends Controller
{
    public function GraficoAllRequisicionesStatus(Request $request)
    {
        $firstday = $request->input('fd');
        $lastday = $request->input('ld');
        $allRequisiciones = Requisicion::where('borrado', 0)
            ->whereBetween('created_at', [$firstday . ' 00:00:00', $lastday . ' 23:59:59']) // Filtra por rango de fechas
            ->get();
        $allEstatus = Estatus::select('id', 'name')->get();
        $requiEstatus = [];
        foreach ($allEstatus as $estatus) {
            $reqEstatusId = ['estatus' => $estatus->name, 'total' => 0];
            foreach ($allRequisiciones as $requi) {
                if ($estatus->id == $requi->estatus_id) {
                    $reqEstatusId['total']++;
                }
            }
            array_push($requiEstatus, $reqEstatusId);
        }

        return response()->json(['estatus' => $allEstatus, 'contador' => $requiEstatus]);
    }

    public function GraficoAllRequisicionesProveedores(Request $request)
    {
        $firstday = $request->input('fd');
        $lastday = $request->input('ld');

        $requisiciones = Requisicion::selectRaw('COUNT(proveedor) as total, proveedor')
            ->whereNotNull('proveedor') // r.proveedor IS NOT NULL
            ->whereNotNull('ordenCompra')
            ->whereBetween('created_at', [$firstday . ' 00:00:00', $lastday . ' 23:59:59'])
            ->where('borrado', 0)       // borrado = 0
            ->groupBy('proveedor')      // GROUP BY r.proveedor
            ->orderByDesc('total')      // ORDER BY total DESC
            ->get();

        return response()->json(['proveedores' => $requisiciones]);
    }

    public function GraficoAllRequisicionesUnidades(Request $request)
    {
        $firstday = $request->input('fd');
        $lastday = $request->input('ld');

        $requisiciones = Requisicion::selectRaw('COUNT(unidad) as total, unidad')
            ->whereBetween('created_at', [$firstday . ' 00:00:00', $lastday . ' 23:59:59'])
            ->whereNotNull('unidad')
            ->where('unidad', '!=', '')
            ->where('borrado', 0)
            ->where('estatus_id', '!=', 9)
            ->groupBy('unidad')
            ->orderByDesc('total')
            ->get();

        return response()->json($requisiciones);
    }

    public function GraficoAllGastosPorUnidades(Request $request)
    {
        $firstday = $request->input('fd');
        $lastday = $request->input('ld');

        $requisicionesIDUnidades = Requisicion::select('id', 'unidad')
            ->whereBetween('created_at', [$firstday . ' 00:00:00', $lastday . ' 23:59:59'])
            ->whereNotNull('unidad')
            ->where('unidad', '!=', '')
            ->where('borrado', 0)
            ->where('estatus_id', '!=', 9)
            ->get();

        $cotizacionesUnidades = Cotizacion::select('id')
            ->whereIn('requisicion_id', $requisicionesIDUnidades->pluck('id'))
            ->where('estatus', 1)->get();

        $cotizacionesUnidadesReqis = Cotizacion::select('id', 'requisicion_id')
            ->whereIn('requisicion_id', $requisicionesIDUnidades->pluck('id'))
            ->where('estatus', 1)
            ->orderBy('requisicion_id', 'ASC')
            ->get();

        $detalleCotizacionesUnidades = DetalleCotizacion::select('*')
            ->where('autorizado', 1)
            ->whereIn('cotizacion_id', $cotizacionesUnidades)
            ->orderByDesc('cotizacion_id')
            ->get();

        $detalleCotizacionesUnidadesSuma = DetalleCotizacion::select('cotizacion_id', DB::raw('SUM(precio * cantidad) as total'))
            ->whereNotNull('cotizacion_id')
            ->where('autorizado', 1)
            ->whereIn('cotizacion_id', $cotizacionesUnidades)
            ->groupBy('cotizacion_id')
            ->get();

        $requisTotalCotizacion = [];

        foreach ($cotizacionesUnidadesReqis as $cur) {
            foreach ($detalleCotizacionesUnidadesSuma as $dcus) {
                if ($cur->id == $dcus->cotizacion_id) {
                    array_push($requisTotalCotizacion, [
                        'requisicion_id' => $cur->requisicion_id,
                        'cotizacion_id' => $dcus->cotizacion_id,
                        'total' => $dcus->total
                    ]);
                }
            }
        }

        $requisTotalCotizacionUnidad = [];

        foreach ($requisicionesIDUnidades as $ridu) {
            foreach ($requisTotalCotizacion as $rtcu) {
                if ($ridu->id == $rtcu['requisicion_id']) {
                    array_push($requisTotalCotizacionUnidad, [
                        'requisicion_id' => $rtcu['requisicion_id'],
                        'cotizacion_id' => $rtcu['cotizacion_id'],
                        'total' => $rtcu['total'],
                        'unidad' => $ridu->unidad
                    ]);
                }
            }
        }


        return response()->json(['detalle' => $detalleCotizacionesUnidades, 'totales' => $detalleCotizacionesUnidadesSuma, 'detalleTotales' => $requisTotalCotizacionUnidad]);
    }
}
