@extends('layouts.app')

@section('title', 'Inspecciones Informales')

@section('css')
<style>
    .boleta-rosa-header { background-color: #f76c83 !important; color: white !important; }
    .checkbox-rosa:checked { background-color: #f76c83 !important; border-color: #f76c83 !important; }
    .badge-rosa { background-color: #f76c83 !important; color: white; }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Inspecciones Informales</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-file-invoice me-1"></i> Recorridos de Rutina / Infracciones
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#infModal" onclick="prepararAlta()">
        <i class="fa-solid fa-file-circle-plus me-2"></i> Levantar Acta de Infracción
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #f76c83 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-clipboard-list me-2 text-danger"></i> Registro Histórico de Boletas de Campo
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 12%;">Folio / Fecha</th>
                        <th class="py-3">Establecimiento / Giro Informal</th>
                        <th class="py-3">Medidor / Cuenta</th>
                        <th class="py-3">Anomalías Detectadas</th>
                        <th class="py-3">Inspector Asignado</th>
                        <th class="text-center py-3" style="width: 10%;">Estatus</th>
                        <th class="text-center py-3" style="width: 25%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspecciones as $inf)
                    <tr class="border-bottom">
                        <td class="ps-4 py-3">
                            <span class="badge bg-danger font-monospace mb-1" style="font-size: 11px;"># {{ $inf->num_folio }}</span>
                            <small class="d-block text-dark fw-semibold">{{ \Carbon\Carbon::parse($inf->fecha_infraccion)->format('d/m/Y') }}</small>
                            <small class="text-muted font-monospace">{{ $inf->hora_infraccion }}</small>
                        </td>
                        <td class="py-3">
                            <span class="fw-bold d-block text-dark" style="font-size: 14.5px;">{{ $inf->nombre_establecimiento_informal }}</span>
                            <small class="text-muted d-block text-truncate" style="max-width: 230px;"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $inf->domicilio_informal }}</small>
                        </td>
                        <td class="py-3">
                            <small class="d-block text-dark font-monospace">Medidor: <strong>{{ $inf->num_medidor_informal ?? 'N/E' }}</strong></small>
                            <small class="d-block text-secondary font-monospace">Cuenta: <strong>{{ $inf->cuenta_informal ?? 'N/E' }}</strong></small>
                        </td>
                        <td class="py-3">
                            <div class="d-flex flex-wrap gap-1">
                                @if($inf->anomalia_grasas_aceites) <span class="badge badge-rosa text-uppercase" style="font-size: 9.5px;">Grasas / Aceites</span> @endif
                                @if($inf->anomalia_sin_permiso) <span class="badge bg-dark text-uppercase" style="font-size: 9.5px;">Sin Permiso</span> @endif
                                @if($inf->anomalia_residuos_toxicos) <span class="badge bg-warning text-dark text-uppercase" style="font-size: 9.5px;">Tóxicos</span> @endif
                                @if($inf->anomalia_aguas_pluviales) <span class="badge bg-primary text-uppercase" style="font-size: 9.5px;">Pluviales</span> @endif
                                @if($inf->anomalia_sin_registro_banqueta) <span class="badge bg-secondary text-uppercase" style="font-size: 9.5px;">Sin Reg. Banqueta</span> @endif
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="small d-block text-dark fw-semibold"><i class="fa-solid fa-user-check me-1 text-secondary"></i>{{ $inf->inspector->nombre }} {{ $inf->inspector->paterno }}</span>
                        </td>
                        <td class="text-center py-3">
                            @if($inf->status === 'Pendiente')
                                <span class="badge bg-warning text-dark rounded-1 text-uppercase" style="font-size: 11px;">Pendiente</span>
                            @else
                                <span class="badge bg-success rounded-1 text-uppercase" style="font-size: 11px;">{{ $inf->status }}</span>
                            @endif
                        </td>
                        <td class="text-center py-3">
                            <div class="d-inline-flex gap-1 justify-content-center w-100">
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm px-2 py-1 rounded-1 fw-bold" onclick="prepararEdicion({{ json_encode($inf) }})">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                </button>

                                <a href="{{ route('inspecciones_informales.pdf', $inf->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 fw-bold">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Boleta PDF
                                </a>

                                @if($inf->archivo_evidencia_id)
                                <a href="{{ route('archivos_digitales.ver', $inf->archivo_evidencia_id) }}" target="_blank" class="btn btn-sm btn-success shadow-sm px-2 py-1 rounded-1 fw-bold">
                                    <i class="fa-solid fa-camera me-1"></i> Evidencia
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i> No se han capturado boletas informales en este periodo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="infModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header boleta-rosa-header">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fa-solid fa-file-signature me-2"></i> Captura de Boleta de Infracción</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="infForm" action="{{ route('inspecciones_informales.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body bg-light p-4" style="max-height: 70vh; overflow-y: auto;">

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-danger">
                            <i class="fa-solid fa-receipt me-2"></i> Datos de Control Img. Rosa
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Folio Infracción *</label>
                                <input type="text" name="num_folio" id="input_num_folio" class="form-control border-danger font-monospace fw-bold rounded-1" placeholder="Ej: 0228" data-label="NÚMERO DE FOLIO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Fecha del Levantamiento *</label>
                                <input type="date" name="fecha_infraccion" id="input_fecha_infraccion" class="form-control rounded-1" data-label="FECHA DE INFRACCIÓN">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Hora (Siendo las:) *</label>
                                <input type="time" name="hora_infraccion" id="input_hora_infraccion" class="form-control rounded-1" data-label="HORA DE INFRACCIÓN">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-dark">
                            <i class="fa-solid fa-store me-2"></i> Localización / Infractor Manuscríto
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Establecimiento *</label>
                                <input type="text" name="nombre_establecimiento_informal" id="input_nombre_establecimiento_informal" class="form-control rounded-1" placeholder="Ej: Cocina Económica Yumel" data-label="NOMBRE COMERCIAL">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Domicilio *</label>
                                <input type="text" name="domicilio_informal" id="input_domicilio_informal" class="form-control rounded-1" placeholder="Ej: Mercado Garmendia Locales 10 y 11" data-label="DOMICILIO FÍSICO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Medidor</label>
                                <input type="text" name="num_medidor_informal" id="input_num_medidor_informal" class="form-control rounded-1" placeholder="Ej: 355970" data-label="MEDIDOR">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Cuenta Contrato JAPAC</label>
                                <input type="text" name="cuenta_informal" id="input_cuenta_informal" class="form-control rounded-1" placeholder="Ej: 1-15240" data-label="CUENTA JAPAC">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Señas Particulares</label>
                                <input type="text" name="señas_particulares" id="input_señas_particulares" class="form-control rounded-1" placeholder="Ej: Fachada Económica" data-label="SEÑAS PARTICULARES">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4" style="background-color: #fff5f6 !important;">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-danger">
                            <i class="fa-solid fa-square-check me-2"></i> Tipo de Infracción (Infringiendo Ley de Agua y Alcantarillado)
                        </h6>
                        <div class="row g-2 ps-2">
                            <div class="col-12 form-check form-switch mb-1">
                                <input class="form-check-input checkbox-rosa" type="checkbox" name="anomalia_grasas_aceites" id="check_grasas" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="check_grasas">DESCARGAR GRASAS Y ACEITES AL DRENAJE SANITARIO</label>
                            </div>
                            <div class="col-12 form-check form-switch mb-1">
                                <input class="form-check-input checkbox-rosa" type="checkbox" name="anomalia_sin_permiso" id="check_sin_permiso" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="check_sin_permiso">DESCARGAR AGUAS RESIDUALES SIN PERMISO</label>
                            </div>
                            <div class="col-12 form-check form-switch mb-1">
                                <input class="form-check-input checkbox-rosa" type="checkbox" name="anomalia_residuos_toxicos" id="check_toxicos" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="check_toxicos">DESCARGAR RESIDUOS TÓXICOS O PROHIBIDOS AL DRENAJE SANITARIO</label>
                            </div>
                            <div class="col-12 form-check form-switch mb-1">
                                <input class="form-check-input checkbox-rosa" type="checkbox" name="anomalia_aguas_pluviales" id="check_pluviales" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="check_pluviales">DESCARGAR AGUAS PLUVIALES AL DRENAJE SANITARIO</label>
                            </div>
                            <div class="col-12 form-check form-switch mb-1">
                                <input class="form-check-input checkbox-rosa" type="checkbox" name="anomalia_sin_registro_banqueta" id="check_banqueta" value="1">
                                <label class="form-check-label fw-semibold text-dark" for="check_banqueta">NO CUENTA CON REGISTRO DE BANQUETA</label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-dark">
                            <i class="fa-solid fa-comment-medical me-2"></i> Observaciones y Dictamen Técnico de Campo
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Descripción detallada de la anomalía *</label>
                                <textarea name="observaciones_campo" id="input_observaciones_campo" rows="3" class="form-control rounded-1" placeholder="Ej: Se infracciona porque al revisar el establecimiento se encontró bastante sólido y grasa tapando..." data-label="OBSERVACIONES DE CAMPO"></textarea>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-uppercase text-muted">Recibo Notificación (Se dejó a:) *</label>
                                <input type="text" name="recibio_notificacion" id="input_recibio_notificacion" class="form-control rounded-1" placeholder="Ej: Se dejó original colgado / Firmó Encargado" data-label="ESTATUS DE NOTIFICACIÓN">
                            </div>
                            <div class="col-md-4" id="contenedor_status" style="display: none;">
                                <label class="form-label fw-bold small text-uppercase text-danger">Estatus del Proceso</label>
                                <select name="status" id="input_status" class="form-select border-danger">
                                    <option value="Pendiente">PENDIENTE</option>
                                    <option value="En Proceso Multa">EN PROCESO MULTA</option>
                                    <option value="Solucionado">SOLUCIONADO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-primary">
                            <i class="fa-solid fa-camera me-2"></i> Digitalización / Evidencia Física de Campo
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Adjuntar Fotografía o Escaneo de la Boleta Rosa Levantada (.jpg, .png, .pdf)</label>
                                <input type="file" name="evidencia_archivo" id="input_evidencia_archivo" class="form-control rounded-1" accept="image/*,application/pdf">
                                <div id="contenedor_descarga_evidencia" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn rounded-1 px-4 text-white font-weight-bold shadow-sm" style="background-color: #0056b3;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Acta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ title: '¡Completado!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0056b3' });
        @endif
        @if(session('error'))
            Swal.fire({ title: 'Atención', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#0056b3' });
        @endif
    });

    document.getElementById('infForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const campos = ['input_num_folio', 'input_fecha_infraccion', 'input_hora_infraccion', 'input_nombre_establecimiento_informal', 'input_domicilio_informal', 'input_observaciones_campo', 'input_recibio_notificacion'];

        for (let id of campos) {
            const el = document.getElementById(id);
            if(el && !el.value.trim()){
                Swal.fire({ title: 'Campo requerido', text: `El campo ${el.getAttribute('data-label')} no puede estar vacío.`, icon: 'warning', confirmButtonColor: '#0056b3' });
                return false;
            }
        }
        this.submit();
    });

    function prepararAlta() {
        const form = document.getElementById('infForm');
        form.reset();
        form.action = "{{ route('inspecciones_informales.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-file-signature me-2 text-info"></i> Captura de Boleta de Infracción';
        document.getElementById('contenedor_status').style.display = 'none';
        document.getElementById('contenedor_descarga_evidencia').style.display = 'none';
        document.getElementById('input_evidencia_archivo').value = '';
    }

    function prepararEdicion(inf) {
        prepararAlta();
        const form = document.getElementById('infForm');
        form.action = `/inspecciones-informales/${inf.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerHTML = `<i class="fa-solid fa-pen-to-square me-2 text-warning"></i> Editar Infracción Folio: ${inf.num_folio}`;

        document.getElementById('input_num_folio').value = inf.num_folio;
        document.getElementById('input_fecha_infraccion').value = inf.fecha_infraccion.substring(0,10);
        document.getElementById('input_hora_infraccion').value = inf.hora_infraccion;
        document.getElementById('input_nombre_establecimiento_informal').value = inf.nombre_establecimiento_informal;
        document.getElementById('input_domicilio_informal').value = inf.domicilio_informal;
        document.getElementById('input_num_medidor_informal').value = inf.num_medidor_informal || '';
        document.getElementById('input_cuenta_informal').value = inf.cuenta_informal || '';
        document.getElementById('input_señas_particulares').value = inf.señas_particulares || '';
        document.getElementById('input_observaciones_campo').value = inf.observaciones_campo || '';
        document.getElementById('input_recibio_notificacion').value = inf.recibio_notificacion || '';

        document.getElementById('check_grasas').checked = inf.anomalia_grasas_aceites;
        document.getElementById('check_sin_permiso').checked = inf.anomalia_sin_permiso;
        document.getElementById('check_toxicos').checked = inf.anomalia_residuos_toxicos;
        document.getElementById('check_pluviales').checked = inf.anomalia_aguas_pluviales;
        document.getElementById('check_banqueta').checked = inf.anomalia_sin_registro_banqueta;

        document.getElementById('input_status').value = inf.status || 'Pendiente';
        document.getElementById('contenedor_status').style.display = 'block';

        // Mapear dinámicamente si existe evidencia guardada por el inspector
        const divEvidencia = document.getElementById('contenedor_descarga_evidencia');
        if (inf.archivo_evidencia_id) {
            divEvidencia.style.display = 'block';
            divEvidencia.innerHTML = `
                <div class="alert alert-secondary d-flex align-items-center justify-content-between p-2 rounded-1 mt-2" style="font-size: 13px;">
                    <span><i class="fa-solid fa-paperclip me-2 text-success"></i> Hay una boleta escaneada cargada.</span>
                    <a href="/archivos-digitales/${inf.archivo_evidencia_id}" target="_blank" class="btn btn-xs btn-success text-white px-2 py-1 rounded-1 fw-bold text-uppercase">
                        <i class="fa-solid fa-eye me-1"></i> Ver Evidencia Físcia
                    </a>
                </div>`;
        } else {
            divEvidencia.style.display = 'none';
        }

        (new bootstrap.Modal(document.getElementById('infModal'))).show();
    }
</script>
@endsection
