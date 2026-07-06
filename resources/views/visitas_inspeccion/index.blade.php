@extends('layouts.app')

@section('title', 'Visitas de Inspección')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Visitas de Inspección</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-file-shield me-1"></i> Procedimientos Formales / Órdenes de Diligencia
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#viModal" onclick="prepararAlta()">
        <i class="fa-solid fa-folder-plus me-2"></i> Aperturar Orden de Visita
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-folder-open me-2 text-primary"></i> Expedientes Activos de Visitas Formales
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3" style="width: 15%;">No. Diligencia / Oficio</th>
                        <th class="py-3">Establecimiento Regulado</th>
                        <th class="py-3">Fecha y Hora de Visita</th>
                        <th class="py-3">Dictamen y Observaciones</th>
                        <th class="text-center py-3" style="width: 12%;">Estatus</th>
                        <th class="text-center py-3" style="width: 25%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitas as $v)
                    <tr class="border-bottom">
                        <td class="ps-4 py-3">
                            <span class="badge bg-dark font-monospace mb-1" style="font-size: 11px;">Ref: {{ $v->num_visita_inspeccion }}</span>
                            <small class="d-block text-primary fw-bold font-monospace">{{ $v->num_oficioVI }}</small>
                        </td>
                        <td class="py-3">
                            <span class="fw-bold d-block text-dark" style="font-size: 14.5px;">{{ $v->establecimiento->nombre_establecimiento }}</span>
                            <small class="text-muted d-block font-monospace">Cuenta JAPAC: {{ $v->establecimiento->cuenta ?? 'N/E' }}</small>
                        </td>
                        <td class="py-3">
                            <span class="fw-semibold text-dark"><i class="fa-solid fa-calendar-day me-1 text-muted"></i>{{ \Carbon\Carbon::parse($v->fechavisita_inspeccion)->format('d/m/Y H:i') }} Hrs</span>
                        </td>
                        <td class="py-3">
                            <small class="text-dark text-wrap d-block" style="max-width: 300px;">
                                {{ $v->observaciones ?? 'Sin observaciones asentadas al momento del levantamiento.' }}
                            </small>
                        </td>
                        <td class="text-center py-3">
                            @if($v->status === 'Pendiente')
                                <span class="badge bg-warning text-dark rounded-1 text-uppercase" style="font-size: 10.5px;">Pendiente</span>
                            @elseif($v->status === 'Notificado')
                                <span class="badge bg-info text-white rounded-1 text-uppercase" style="font-size: 10.5px;">Notificado</span>
                            @else
                                <span class="badge bg-success rounded-1 text-uppercase" style="font-size: 10.5px;">{{ $v->status }}</span>
                            @endif
                        </td>
                        <td class="text-center py-3">
                            <div class="d-inline-flex gap-1 justify-content-center w-100">
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm px-2 py-1 rounded-1 fw-bold" onclick="prepararEdicion({{ json_encode($v) }})">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                </button>

                                <a href="{{ route('visitas_inspeccion.pdf', $v->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 fw-bold">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Oficio PDF
                                </a>

                                @if($v->archivo_evidencia_id)
                                <a href="{{ route('archivos_digitales.ver', $v->archivo_evidencia_id) }}" target="_blank" class="btn btn-sm btn-success shadow-sm px-2 py-1 rounded-1 fw-bold">
                                    <i class="fa-solid fa-camera me-1"></i> Acta Escaneada
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i> No se han aperturado expedientes formales en este periodo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="viModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fa-solid fa-file-shield me-2"></i> Captura de Diligencia Formal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="viForm" action="{{ route('visitas_inspeccion.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body bg-light p-4" style="max-height: 70vh; overflow-y: auto;">

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-primary">
                            <i class="fa-solid fa-receipt me-2"></i> Datos de Nomenclatura Legal (JAPAC)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Visita Inspección *</label>
                                <input type="text" name="num_visita_inspeccion" id="input_num_visita_inspeccion" class="form-control font-monospace fw-bold" placeholder="Ej: V.I. 001/17" data-label="NÚMERO DE VISITA DE INSPECCIÓN">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Oficio Dictamen (VI) *</label>
                                <input type="text" name="num_oficioVI" id="input_num_oficioVI" class="form-control border-primary font-monospace fw-bold" placeholder="Ej: No.: D.J. 008/17" data-label="NÚMERO DE OFICIO DE INSPECCIÓN">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-dark">
                            <i class="fa-solid fa-building-user me-2"></i> Comercio Regulado y Fecha de Ejecución
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold small text-uppercase text-muted">Seleccionar Establecimiento *</label>
                                <select name="establecimiento_id" id="input_establecimiento_id" class="form-select" data-label="ESTABLECIMIENTOS">
                                    <option value="">-- Seleccione del Padrón Activo --</option>
                                    @foreach($establecimientos as $e)
                                        <option value="{{ $e->id }}">{{ $e->nombre_establecimiento }} (Cuenta: {{ $e->cuenta ?? 'S/C' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold small text-uppercase text-muted">Fecha y Hora de la Diligencia *</label>
                                <input type="datetime-local" name="fechavisita_inspeccion" id="input_fechavisita_inspeccion" class="form-control" data-label="FECHA Y HORA DE LA VISITA">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-dark">
                            <i class="fa-solid fa-comment-medical me-2"></i> Hechos Constatados y Dictamen
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Observaciones de Campo *</label>
                                <textarea name="observaciones" id="input_observaciones" rows="3" maxlength="370" class="form-control" placeholder="Escriba los hallazgos del lodo, muestreo, trampas de grasa..." data-label="OBSERVACIONES"></textarea>
                                <small class="text-muted d-block mt-1 text-end">Máximo 370 caracteres obligatorios por migración.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-danger">Estatus del Proceso Formal</label>
                                <select name="status" id="input_status" class="form-select border-danger">
                                    <option value="Notificado">NOTIFICADO</option>
                                    <option value="Pendiente">PENDIENTE</option>
                                    <option value="Concluido">CONCLUIDO</option>
                                    <option value="Irregularidad">CON IRREGULARIDAD</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-success">
                            <i class="fa-solid fa-camera me-2"></i> Carga de Acta Firmada (Evidencia)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Adjuntar Oficio Escaneado y Firmado con Sellos (.jpg, .png, .pdf)</label>
                                <input type="file" name="evidencia_archivo" id="input_evidencia_archivo" class="form-control" accept="image/*,application/pdf">
                                <div id="contenedor_descarga_vi" class="mt-2" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn rounded-1 px-4 text-white font-weight-bold shadow-sm" style="background-color: #0056b3;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Archivar Diligencia
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
    });

    document.getElementById('viForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const campos = ['input_num_visita_inspeccion', 'input_num_oficioVI', 'input_establecimiento_id', 'input_fechavisita_inspeccion', 'input_observaciones'];

        for (let id of campos) {
            const el = document.getElementById(id);
            if(el && !el.value.trim()){
                Swal.fire({ title: 'Campo requerido', text: `El campo ${el.getAttribute('data-label')} es obligatorio para actas formales.`, icon: 'warning', confirmButtonColor: '#0056b3' });
                return false;
            }
        }
        this.submit();
    });

    function prepararAlta() {
        const form = document.getElementById('viForm');
        form.reset();
        form.action = "{{ route('visitas_inspeccion.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-file-shield me-2 text-info"></i> Aperturar Orden de Visita';
        document.getElementById('contenedor_descarga_vi').style.display = 'none';
        document.getElementById('input_evidencia_archivo').value = '';
    }

    function prepararEdicion(v) {
        prepararAlta();
        const form = document.getElementById('viForm');
        form.action = `/visitas-inspeccion/${v.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerHTML = `<i class="fa-solid fa-pen-to-square me-2 text-warning"></i> Editar Diligencia Oficio: ${v.num_oficioVI}`;

        document.getElementById('input_num_visita_inspeccion').value = v.num_visita_inspeccion;
        document.getElementById('input_num_oficioVI').value = v.num_oficioVI;
        document.getElementById('input_establecimiento_id').value = v.establecimiento_id;

        // Formatear la fecha para input datetime-local (YYYY-MM-DDTHH:MM)
        if(v.fechavisita_inspeccion) {
            document.getElementById('input_fechavisita_inspeccion').value = v.fechavisita_inspeccion.substring(0, 16);
        }

        document.getElementById('input_observaciones').value = v.observaciones || '';
        document.getElementById('input_status').value = v.status || 'Notificado';

        const divEvidencia = document.getElementById('contenedor_descarga_vi');
        if (v.archivo_evidencia_id) {
            divEvidencia.style.display = 'block';
            divEvidencia.innerHTML = `
                <div class="alert alert-secondary d-flex align-items-center justify-content-between p-2 rounded-1 mt-2" style="font-size: 13px;">
                    <span><i class="fa-solid fa-paperclip me-2 text-success"></i> Hay una orden escaneada en el expediente.</span>
                    <a href="/archivos-digitales/${v.archivo_evidencia_id}" target="_blank" class="btn btn-xs btn-success text-white px-2 py-1 rounded-1 fw-bold text-uppercase">
                        <i class="fa-solid fa-eye me-1"></i> Ver Acta Firmada
                    </a>
                </div>`;
        } else {
            divEvidencia.style.display = 'none';
        }

        (new bootstrap.Modal(document.getElementById('viModal'))).show();
    }
</script>
@endsection
