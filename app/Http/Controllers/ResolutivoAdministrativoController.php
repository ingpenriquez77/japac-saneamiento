<?php

namespace App\Http\Controllers;

use App\Models\ResolutivoAdministrativo;
use App\Models\InicioProcedimiento;
use App\Models\ArchivoBinario;
use Illuminate\Http\Request;
use Pdf;

class ResolutivoAdministrativoController extends Controller
{
    public function index()
    {
        $resolutivos = ResolutivoAdministrativo::with(['inicioProcedimiento.visita.establecimiento', 'usuario'])->orderBy('id', 'desc')->get();
        // Solo traemos los procedimientos que están vigentes para poder dictaminarles resolución
        $procedimientos = InicioProcedimiento::with('visita.establecimiento')->where('status', 'En Proceso')->orderBy('id', 'desc')->get();

        return view('resolutivo_administrativos.index', compact('resolutivos', 'procedimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inicio_procedimiento_id' => 'required|exists:inicio_procedimientos,id',
            'num_resolutivo'          => 'required|string|max:50|unique:resolutivo_administrativos,num_resolutivo',
            'fecha_resolucion'        => 'required|date',
            'monto_sancion_pesos'     => 'required|numeric|min:0',
            'considerandos_legales'   => 'required|string',
        ]);

        $res = ResolutivoAdministrativo::create([
            'inicio_procedimiento_id' => $request->inicio_procedimiento_id,
            'user_id'                 => auth()->id(),
            'num_resolutivo'          => $request->num_resolutivo,
            'fecha_resolucion'        => $request->fecha_resolucion,
            'monto_sancion_pesos'     => $request->monto_sancion_pesos,
            'sancion_adicional'       => $request->sancion_adicional,
            'considerandos_legales'   => $request->considerandos_legales,
            'status_final'            => 'Notificado'
        ]);

        // Cambiamos el estatus en cadena del juicio previo de origen
        \App\Models\InicioProcedimiento::where('id', $request->inicio_procedimiento_id)->update(['status' => 'Sancionado']);

        // Cargamos relaciones para que no salgan vacías en el compilador de DomPDF
        $res->load('inicioProcedimiento.visita.establecimiento');

        // Compilación HTML a PDF
        $htmlPdf = view('resolutivo_administrativos.pdf_template', ['res' => $res])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        // 🔗 INSERCIÓN CORRECTA EN LA TABLA archivos_binarios
        // Se mapea bajo el método "archivos()" de tu modelo polimórfico
        $res->archivos()->create([
            'nombre_archivo'   => 'SISTEMA_RES_' . str_replace(['/', ' ', '.'], '_', $res->num_resolutivo) . '.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfRenderizado)
        ]);

        return redirect()->route('resolutivo_administrativos.index')->with('success', 'Resolutivo Administrativo dictaminado e insertado con éxito.');
    }

    public function verPdf($id)
    {
        $res = ResolutivoAdministrativo::findOrFail($id);
        $doc = $res->archivos()->first();

        if (!$doc) abort(404, 'No se encontró el archivo digital del resolutivo en la base de datos.');

        return response(base64_decode($doc->contenido_base64), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $doc->nombre_archivo . '"');
    }
}
