<?php

namespace App\Http\Controllers;

use App\Models\InicioProcedimiento;
use App\Models\VisitaInspeccion;
use App\Models\ArchivoBinario;
use Illuminate\Http\Request;
use Pdf;

class InicioProcedimientoController extends Controller
{
    public function index()
    {
        $procedimientos = InicioProcedimiento::with(['visita.establecimiento', 'usuario'])->orderBy('id', 'desc')->get();
        $visitas = VisitaInspeccion::with('establecimiento')->orderBy('id', 'desc')->get();

        return view('inicio_procedimientos.index', compact('procedimientos', 'visitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'visita_inspeccion_id' => 'required|exists:visita_inspeccions,id',
            'num_oficio_inicio'    => 'required|string|max:50|unique:inicio_procedimientos,num_oficio_inicio',
            'fecha_notificacion'   => 'required|date',
            'fundamento_legal'     => 'required|string|max:255',
            'hechos_motivo'        => 'required|string',
        ]);

        $proc = InicioProcedimiento::create([
            'visita_inspeccion_id' => $request->visita_inspeccion_id,
            'user_id'              => auth()->id(),
            'num_oficio_inicio'    => $request->num_oficio_inicio,
            'fecha_notificacion'   => $request->fecha_notificacion,
            'fundamento_legal'     => $request->fundamento_legal,
            'hechos_motivo'        => $request->hechos_motivo,
            'plazo_concedido'      => $request->plazo_concedido ?? '5 días hábiles',
            'status'               => 'En Proceso'
        ]);

        // Cargar la relación para evitar errores de objetos vacíos en el PDF
        $proc->load('visita.establecimiento');

        // Generación de la plantilla digital polimórfica en PDF
        $htmlPdf = view('inicio_procedimientos.pdf_template', ['proc' => $proc])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        // 🔗 Guardado usando la relación mágica del modelo
        $proc->archivos()->create([
            'nombre_archivo'   => 'SISTEMA_PROC_' . str_replace(['/', ' ', '.'], '_', $proc->num_oficio_inicio) . '.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfRenderizado)
        ]);

        return redirect()->route('inicio_procedimientos.index')->with('success', 'Inicio de procedimiento administrativo aperturado.');
    }

    public function verPdf($id)
    {
        $proc = InicioProcedimiento::findOrFail($id);

        // 🎯 Buscamos el archivo usando la relación exacta para no errar en los nombres de las columnas
        $doc = $proc->archivos()->first();

        if (!$doc) abort(404, 'No se encontró el documento PDF generado en la base de datos.');

        return response(base64_decode($doc->contenido_base64), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $doc->nombre_archivo . '"');
    }
}
