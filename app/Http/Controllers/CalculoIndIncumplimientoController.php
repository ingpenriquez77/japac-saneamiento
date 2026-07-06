<?php

namespace App\Http\Controllers;

use App\Models\CalculoIndIncumplimiento;
use App\Models\Establecimiento;
use App\Models\ArchivoBinario;
use Illuminate\Http\Request;
use Pdf;

class CalculoIndIncumplimientoController extends Controller
{
    public function index()
    {
        $calculos = CalculoIndIncumplimiento::with(['establecimiento', 'usuario'])->orderBy('id', 'desc')->get();
        $establecimientos = Establecimiento::where('status', 'Activo')->orderBy('nombre_establecimiento')->get();

        return view('calculo_incumplimientos.index', compact('calculos', 'establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id'      => 'required|exists:establecimientos,id',
            'fecha_muestreo'          => 'required|date',
            'gasto_medio_diario_lps'  => 'required|numeric|min:0',
            'resultado_dbo'           => 'required|numeric|min:0',
            'resultado_sst'           => 'required|numeric|min:0',
            'resultado_gya'           => 'required|numeric|min:0',
            'laboratorio_analisis'    => 'required|string',
        ]);

        $gasto = floatval($request->gasto_medio_diario_lps);
        $dbo = floatval($request->resultado_dbo);
        $sst = floatval($request->resultado_sst);
        $gya = floatval($request->resultado_gya);

        // 🎯 Límites Reales Correspondientes al Promedio Mensual Marcados en el Formato de JAPAC
        $limDbo = 150.00;
        $limSst = 150.00;
        $limGya = 50.00;

        // 1. Automatización del Volumen Mensual (m³)
        $volumenMensual = $gasto * 86400 * 30 / 1000;

        // 2. Cálculo de Índices Individuales Exactos (Fórmula: (Resultado - Límite) / Límite)
        $indDbo = ($dbo > $limDbo) ? (($dbo - $limDbo) / $limDbo) : 0.00;
        $indSst = ($sst > $limSst) ? (($sst - $limSst) / $limSst) : 0.00;
        $indGya = ($gya > $limGya) ? (($gya - $limGya) / $limGya) : 0.00;

        // 3. Determinar el Contaminante Predominante (El de Índice Mayor)
        $maxIndice = max($indDbo, $indSst, $indGya);
        $contaminante = 'NINGUNO';
        $resMayor = 0.00; $limMayor = 0.00;

        if ($maxIndice === $indDbo && $indDbo > 0) {
            $contaminante = 'DBO'; $resMayor = $dbo; $limMayor = $limDbo;
        } elseif ($maxIndice === $indSst && $indSst > 0) {
            $contaminante = 'SST'; $resMayor = $sst; $limMayor = $limSst;
        } elseif ($maxIndice === $indGya && $indGya > 0) {
            $contaminante = 'GYA'; $resMayor = $gya; $limMayor = $limGya;
        }

        // 4. Carga Contaminante Excedente en Kilogramos Exacta
        $cargaKg = 0.00;
        if ($maxIndice > 0) {
            $cargaKg = (($resMayor - $limMayor) / 1000) * $volumenMensual;
        }

        // 5. Cotizador Progresivo según la TABLA III Oficial de JAPAC
        $cuota = 0.000;
        if ($maxIndice > 0.00 && $maxIndice <= 0.10)  $cuota = 0.000;  // Exento
        if ($maxIndice > 0.10 && $maxIndice <= 1.00)  $cuota = 5.808;
        if ($maxIndice > 1.00 && $maxIndice <= 2.00)  $cuota = 7.373;
        if ($maxIndice > 2.00 && $maxIndice <= 3.00)  $cuota = 9.043;
        if ($maxIndice > 3.00 && $maxIndice <= 4.00)  $cuota = 9.885;
        if ($maxIndice > 4.00 && $maxIndice <= 5.00)  $cuota = 10.315;
        if ($maxIndice > 5.00)                         $cuota = 10.837;

        // 6. Totales Financieros Coincidentes
        $montoMes = $cargaKg * $cuota;
        $montoAnual = $montoMes * 12;

        // 7. Insertar en la Base de Datos
        $calculo = CalculoIndIncumplimiento::create([
            'establecimiento_id'         => $request->establecimiento_id,
            'user_id'                    => auth()->id(),
            'fecha_muestreo'             => $request->fecha_muestreo,
            'laboratorio_analisis'       => $request->laboratorio_analisis,
            'gasto_medio_diario_lps'     => $gasto,
            'volumen_mensual_m3'         => $volumenMensual,
            'resultado_dbo'              => $dbo,
            'resultado_sst'              => $sst,
            'resultado_gya'              => $gya,
            'limite_dbo'                 => $limDbo,
            'limite_sst'                 => $limSst,
            'limite_gya'                 => $limGya,
            'indice_dbo'                 => $indDbo,
            'indice_sst'                 => $indSst,
            'indice_gya'                 => $indGya,
            'contaminante_predominante'  => $contaminante,
            'indice_predominante_final'  => $maxIndice,
            'carga_contaminante_kg'      => $cargaKg,
            'cuota_por_kg'               => $cuota,
            'monto_pagar_mes'            => $montoMes,
            'monto_pagar_anual'          => $montoAnual,
            'observaciones'              => $request->observaciones
        ]);

        return redirect()->route('calculo_incumplimientos.index')->with('success', 'Hoja de cálculo generada y tasada con éxito.');
    }

    /**
     * 🚀 RENDERIZADOR: Compila y despliega el PDF oficial calcado de la hoja impresa
     */
    public function verPdf($id)
    {
        $calculo = CalculoIndIncumplimiento::with('establecimiento')->findOrFail($id);

        $htmlPdf = view('calculo_incumplimientos.pdf_template', compact('calculo'))->render();
        $pdf = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Calculo_Cuotas_Folio_'.$calculo->id.'.pdf"');
    }

    public function destroy($id)
    {
        CalculoIndIncumplimiento::findOrFail($id)->delete();
        return redirect()->route('calculo_incumplimientos.index')->with('success', 'Cálculo financiero eliminado del historial.');
    }
}
