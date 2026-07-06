<?php

namespace App\Http\Controllers;

use App\Models\InspeccionInformal;
use App\Models\ArchivoBinario; // 👈 Modelo centralizado universal
use Illuminate\Http\Request;
use Pdf;

class InspeccionInformalController extends Controller
{
    public function index()
    {
        // Cargamos la relación polimórfica de archivos asociados a cada boleta
        $inspecciones = InspeccionInformal::with(['inspector', 'archivoPdf'])->orderBy('id', 'desc')->get();

        // Mapeamos los registros para detectar el ID de la evidencia física subida a mano por el usuario
        $inspecciones->transform(function($inf) {
            $evidencia = $inf->archivoPdf->first(function($archivo) {
                return str_contains($archivo->nombre_archivo, 'EVIDENCIA_FOLIO_');
            });
            $inf->archivo_evidencia_id = $evidencia ? $evidencia->id : null;
            return $inf;
        });

        return view('inspecciones_informales.index', compact('inspecciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'num_folio'                       => 'required|string|max:20|unique:inspeccion_informal,num_folio',
            'fecha_infraccion'                => 'required|date',
            'hora_infraccion'                 => 'required',
            'nombre_establecimiento_informal' => 'required|string|max:150',
            'domicilio_informal'              => 'required|string|max:255',
            'num_medidor_informal'            => 'nullable|string|max:50',
            'cuenta_informal'                 => 'nullable|string|max:50',
            'señas_particulares'              => 'nullable|string|max:150',
            'observaciones_campo'             => 'nullable|string',
            'recibio_notificacion'            => 'nullable|string|max:100',
            'evidencia_archivo'               => 'nullable|file|max:10240',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $data['anomalia_sin_permiso']           = $request->has('anomalia_sin_permiso');
        $data['anomalia_grasas_aceites']        = $request->has('anomalia_grasas_aceites');
        $data['anomalia_residuos_toxicos']      = $request->has('anomalia_residuos_toxicos');
        $data['anomalia_aguas_pluviales']       = $request->has('anomalia_aguas_pluviales');
        $data['anomalia_sin_registro_banqueta'] = $request->has('anomalia_sin_registro_banqueta');

        // 1. Guardamos la boleta en la tabla principal
        $inspeccion = InspeccionInformal::create($data);

        // 2. Si el usuario subió la boleta escaneada a mano, se procesa a Base64 polimórfico
        if ($request->hasFile('evidencia_archivo')) {
            $file = $request->file('evidencia_archivo');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));

            $inspeccion->archivoPdf()->create([
                'nombre_archivo'   => 'EVIDENCIA_FOLIO_' . $inspeccion->num_folio . '_' . time() . '.' . $file->getClientOriginalExtension(),
                'tipo_formato'     => $file->getMimeType(),
                'contenido_base64' => $base64Data
            ]);
        }

        // 3. Generamos e inyectamos de forma paralela el reporte automatizado en PDF del sistema
        $inspeccion->inspector = auth()->user();
        $htmlPdf = view('inspecciones_informales.pdf_template', ['inf' => $inspeccion])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        $inspeccion->archivoPdf()->create([
            'nombre_archivo'   => 'SISTEMA_FOLIO_' . $inspeccion->num_folio . '.pdf',
            'tipo_formato'     => 'application/pdf',
            'contenido_base64' => base64_encode($pdfRenderizado)
        ]);

        return redirect()->route('inspecciones_informales.index')->with('success', 'Acta guardada y documentos archivados.');
    }

    public function update(Request $request, $id)
    {
        $inspeccion = InspeccionInformal::findOrFail($id);

        $request->validate([
            'num_folio'                       => 'required|string|max:20|unique:inspeccion_informal,num_folio,' . $id,
            'fecha_infraccion'                => 'required|date',
            'hora_infraccion'                 => 'required',
            'nombre_establecimiento_informal' => 'required|string|max:150',
            'domicilio_informal'              => 'required|string|max:255',
            'num_medidor_informal'            => 'nullable|string|max:50',
            'cuenta_informal'                 => 'nullable|string|max:50',
            'señas_particulares'              => 'nullable|string|max:150',
            'observaciones_campo'             => 'nullable|string',
            'recibio_notificacion'            => 'nullable|string|max:100',
            'status'                          => 'required|string',
            'evidencia_archivo'               => 'nullable|file|max:10240',
        ]);

        $data = $request->all();
        $data['anomalia_sin_permiso']           = $request->has('anomalia_sin_permiso');
        $data['anomalia_grasas_aceites']        = $request->has('anomalia_grasas_aceites');
        $data['anomalia_residuos_toxicos']      = $request->has('anomalia_residuos_toxicos');
        $data['anomalia_aguas_pluviales']       = $request->has('anomalia_aguas_pluviales');
        $data['anomalia_sin_registro_banqueta'] = $request->has('anomalia_sin_registro_banqueta');

        $inspeccion->update($data);

        // Si se sube una nueva versión de la boleta escaneada, actualizamos o creamos el registro de evidencia
        if ($request->hasFile('evidencia_archivo')) {
            $file = $request->file('evidencia_archivo');
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));

            // 🛠️ CONSULTA DIRECTA SECTORIZADA: Borramos la evidencia antigua usando el Query Builder directo
            ArchivoBinario::where('documento_type', InspeccionInformal::class)
                ->where('documento_id', $inspeccion->id)
                ->where('nombre_archivo', 'LIKE', 'EVIDENCIA_FOLIO_%')
                ->delete();

            $inspeccion->archivoPdf()->create([
                'nombre_archivo'   => 'EVIDENCIA_FOLIO_' . $inspeccion->num_folio . '_' . time() . '.' . $file->getClientOriginalExtension(),
                'tipo_formato'     => $file->getMimeType(),
                'contenido_base64' => $base64Data
            ]);
        }

        // Regeneramos el reporte del sistema actualizado
        $htmlPdf = view('inspecciones_informales.pdf_template', ['inf' => $inspeccion])->render();
        $pdfRenderizado = Pdf::loadHTML($htmlPdf)->setPaper('letter', 'portrait')->output();

        // 🛠️ CONSULTA DIRECTA EVITANDO EL GRAMMAR ERROR: Actualizamos usando el modelo universal directo
        ArchivoBinario::where('documento_type', InspeccionInformal::class)
            ->where('documento_id', $inspeccion->id)
            ->where('nombre_archivo', 'SISTEMA_FOLIO_' . $inspeccion->num_folio . '.pdf')
            ->update(['contenido_base64' => base64_encode($pdfRenderizado)]);

        return redirect()->route('inspecciones_informales.index')->with('success', 'Infracción y documentos actualizados con éxito.');
    }

    /**
     * 🚀 RENDERIZA EL PDF COMPILADO POR EL SISTEMA
     */
    public function verPdf($id)
    {
        $inspeccion = InspeccionInformal::findOrFail($id);

        // Buscamos el archivo del sistema (excluyendo los que son evidencias físicas subidas a mano)
        $documentoSistema = ArchivoBinario::where('documento_type', InspeccionInformal::class)
            ->where('documento_id', $id)
            ->where('nombre_archivo', 'NOT LIKE', 'EVIDENCIA_FOLIO_%') // 👈 Cambiado a NOT LIKE Evidencia
            ->first();

        if (!$documentoSistema || empty($documentoSistema->contenido_base64)) {
            abort(404, 'La boleta automatizada no cuenta con un archivo generado en la base de datos.');
        }

        return response(base64_decode($documentoSistema->contenido_base64), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $documentoSistema->nombre_archivo . '"');
    }

    /**
     * 🚀 CONSULTA UNIVERSAL: Renderiza la evidencia escaneada subida por el usuario
     */
    public function verEvidenciaFisica($archivoId)
    {
        $archivo = ArchivoBinario::findOrFail($archivoId);

        return response(base64_decode($archivo->contenido_base64), 200)
            ->header('Content-Type', $archivo->tipo_formato)
            ->header('Content-Disposition', 'inline; filename="' . $archivo->nombre_archivo . '"');
    }

    public function destroy($id)
    {
        $inspeccion = InspeccionInformal::findOrFail($id);
        $inspeccion->delete();
        return redirect()->route('inspecciones_informales.index')->with('success', 'Acta eliminada del sistema.');
    }
}
