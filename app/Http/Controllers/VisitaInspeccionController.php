<?php

namespace App\Http\Controllers;

use App\Models\VisitaInspeccion;
use App\Models\Establecimiento;
use App\Models\ArchivoBinario;
use Illuminate\Http\Request;
use Pdf;

class VisitaInspeccionController extends Controller
{
    public function index()
    {
        // Cargamos las visitas con su establecimiento y sus archivos adjuntos polimórficos
        $visitas = VisitaInspeccion::with(['establecimiento', 'archivos'])->orderBy('id', 'desc')->get();

        // Mapeamos para identificar si el usuario subió un escaneo real firmado de campo
        $visitas->transform(function($v) {
            $evidencia = $v->archivos->first(function($archivo) {
                return str_contains($archivo->nombre_archivo, 'EVIDENCIA_VI_');
            });
            $v->archivo_evidencia_id = $evidencia ? $evidencia->id : null;
            return $v;
        });

        $establecimientos = Establecimiento::where('status', 'Activo')->orderBy('nombre_establecimiento')->get();

        return view('visitas_inspeccion.index', compact('visitas', 'establecimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'establecimiento_id'      => 'required|exists:establecimientos,id',
            'num_visita_inspeccion'   => 'required|string|max:25|unique:visita_inspeccions,num_visita_inspeccion',
            'fechavisita_inspeccion'  => 'required|date',
            'num_oficioVI'            => 'required|string|max:30|unique:visita_inspeccions,num_oficioVI',
            'status'                  => 'required|string|max:20',
            'observaciones'           => 'nullable|string|max:370',
            'evidencia_archivo'       => 'nullable|file|max:10240' // Escaneo manuscrito opcional (Max 10MB)
        ]);

        // 1. Crear el registro maestro de la diligencia formal
        $visita = VisitaInspeccion::create($request->all());

        // 2. Si se sube el acta física firmada en campo, se archiva en Base64 polimórfico
        if ($request->hasFile('evidencia_archivo')) {
            $file = $request->file('evidencia_archivo');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));

            $visita->archivos()->create([
                'nombre_archivo'   => 'EVIDENCIA_VI_' . str_replace(['/', ' ', '.'], '_', $visita->num_oficioVI) . '.' . $file->getClientOriginalExtension(),
                'tipo_formato'     => $file->getMimeType(),
                'contenido_base64' => $base64Data
            ]);
        }

        // 3. Generar la Orden Oficial Digital en PDF por parte de la JAPAC
        $htmlPdf = view('visitas_inspeccion.pdf_template', ['visita' => $visita])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        $visita->archivos()->create([
            'nombre_archivo'   => 'SISTEMA_VI_' . str_replace(['/', ' ', '.'], '_', $visita->num_oficioVI) . '.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfRenderizado)
        ]);

        return redirect()->route('visitas_inspeccion.index')->with('success', 'Orden de Visita guardada y expedientes archivados.');
    }

    public function update(Request $request, $id)
    {
        $visita = VisitaInspeccion::findOrFail($id);

        $request->validate([
            'establecimiento_id'      => 'required|exists:establecimientos,id',
            'num_visita_inspeccion'   => 'required|string|max:25|unique:visita_inspeccions,num_visita_inspeccion,' . $id,
            'fechavisita_inspeccion'  => 'required|date',
            'num_oficioVI'            => 'required|string|max:30|unique:visita_inspeccions,num_oficioVI,' . $id,
            'status'                  => 'required|string|max:20',
            'observaciones'           => 'nullable|string|max:370',
            'evidencia_archivo'       => 'nullable|file|max:10240'
        ]);

        $visita->update($request->all());

        // Actualizar el escaneo de campo si se subió uno nuevo
        if ($request->hasFile('evidencia_archivo')) {
            $file = $request->file('evidencia_archivo');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));

            ArchivoBinario::where('documento_type', VisitaInspeccion::class)
                ->where('documento_id', $visita->id)
                ->where('nombre_archivo', 'LIKE', 'EVIDENCIA_VI_%')
                ->delete();

            $visita->archivos()->create([
                'nombre_archivo'   => 'EVIDENCIA_VI_' . str_replace(['/', ' ', '.'], '_', $visita->num_oficioVI) . '.' . $file->getClientOriginalExtension(),
                'tipo_formato'     => $file->getMimeType(),
                'contenido_base64' => $base64Data
            ]);
        }

        // Regenerar el reporte institucional actualizado del sistema
        $htmlPdf = view('visitas_inspeccion.pdf_template', ['visita' => $visita])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        ArchivoBinario::where('documento_type', VisitaInspeccion::class)
            ->where('documento_id', $visita->id)
            ->where('nombre_archivo', 'LIKE', 'SISTEMA_VI_%')
            ->update(['contenido_base64' => base64_encode($pdfRenderizado)]);

        return redirect()->route('visitas_inspeccion.index')->with('success', 'Visita de Inspección actualizada.');
    }

    /**
     * 🚀 RENDERIZADOR: Abre el PDF oficial de la JAPAC directo de la BD
     */
    public function verPdf($id)
    {
        $documentoSistema = ArchivoBinario::where('documento_type', VisitaInspeccion::class)
            ->where('documento_id', $id)
            ->where('nombre_archivo', 'LIKE', 'SISTEMA_VI_%')
            ->first();

        if (!$documentoSistema || empty($documentoSistema->contenido_base64)) {
            abort(404, 'La orden de visita no cuenta con un archivo digitalizado.');
        }

        return response(base64_decode($documentoSistema->contenido_base64), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $documentoSistema->nombre_archivo . '"');
    }

    public function destroy($id)
    {
        $visita = VisitaInspeccion::findOrFail($id);

        // Limpiamos los binarios asociados de forma manual para evitar llaves huérfanas
        ArchivoBinario::where('documento_type', VisitaInspeccion::class)
            ->where('documento_id', $visita->id)
            ->delete();

        $visita->delete();
        return redirect()->route('visitas_inspeccion.index')->with('success', 'Registro de Visita eliminado del sistema.');
    }
}
