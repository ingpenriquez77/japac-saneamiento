@extends('layouts.app')

@section('title', 'Control de Establecimientos')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Gestión de Establecimientos</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-building me-1"></i> Padrón de Saneamiento JAPAC
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#estModal" onclick="prepararAlta()">
        <i class="fa-solid fa-circle-plus me-2"></i> Registrar Establecimiento
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-list-check me-2" style="color: #0056b3;"></i> Empresas e Industrias Monitoreadas
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3">Cuenta / Comercial</th>
                        <th class="py-3">Actividad / RFC</th>
                        <th class="py-3">Medidor / Infraestructura</th>
                        <th class="py-3">Ubicación / Contacto</th>
                        <th class="text-center py-3" style="width: 8%;">Estado</th>
                        <th class="text-center py-3" style="width: 15%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($establecimientos as $est)
                    <tr class="border-bottom">

                        <td class="ps-4 py-3">
                            <span class="badge bg-dark mb-1 font-monospace" style="font-size: 10px;">CTA: {{ $est->cuenta }}</span>
                            @if($est->empresa_nueva) <span class="badge bg-info text-white rounded-pill ms-1" style="font-size: 9px;">NUEVA</span> @endif
                            <span class="fw-bold d-block text-dark" style="font-size: 14.5px;">{{ $est->nombre_establecimiento }}</span>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">R.S.: {{ $est->razon_social }}</small>
                        </td>

                        <td class="py-3">
                            <span class="badge bg-light text-primary border border-primary-subtle text-uppercase fw-bold mb-1" style="font-size: 11px;">{{ $est->actividad }}</span>
                            <small class="d-block text-muted font-monospace fw-bold">RFC: {{ $est->rfc }}</small>
                        </td>

                        <td class="py-3">
                            <div class="small mb-1 text-dark"><i class="fa-solid fa-gauge me-1 text-secondary"></i>Medidor: <span class="fw-bold font-monospace">{{ $est->num_medidor }}</span></div>
                            <div class="text-muted" style="font-size: 12px;">
                                <span class="me-2">🥩 Trampas Gra: <strong>{{ $est->trampas_gra ?? 0 }}</strong></span>
                                <span>🪨 SST: <strong>{{ $est->trampas_sst ?? 0 }}</strong></span>
                            </div>
                        </td>

                        <td class="py-3">
                            <span class="small d-block text-dark fw-semibold">{{ $est->calle }} #{{ $est->num_exterior }} @if($est->num_interior) Int. {{ $est->num_interior }} @endif</span>
                            <small class="text-muted d-block text-truncate" style="max-width: 250px; font-size: 12px;">Col. {{ $est->colonia }}, C.P. {{ $est->codigo_postal }}</small>
                            <small class="text-primary d-block" style="font-size: 12px;"><i class="fa-solid fa-phone me-1"></i>{{ $est->telefono }}</small>
                        </td>

                        <td class="text-center py-3">
                            @if($est->status === 'Activo')
                                <span class="badge bg-success px-3 py-1 rounded-1 text-uppercase" style="font-size: 11px;">Activo</span>
                            @else
                                <span class="badge bg-secondary px-3 py-1 rounded-1 text-uppercase" style="font-size: 11px;">{{ $est->status }}</span>
                            @endif
                        </td>

                        <td class="text-center py-3">
                            <div class="d-inline-flex gap-1 justify-content-center w-100">
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm px-2 py-1 rounded-1 d-flex align-items-center" style="font-size: 12px; font-weight: 600;" onclick="prepararEdicion({{ json_encode($est) }})">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Editar
                                </button>

                                @if($est->status === 'Activo')
                                <form action="{{ route('establecimientos.destroy', $est->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmarBaja(this.form)" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 d-flex align-items-center" style="font-size: 12px; font-weight: 600;">
                                        <i class="fa-solid fa-ban me-1"></i> Suspender
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-circle-info fa-2x mb-2 d-block opacity-50"></i> Padrón vacío.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="estModal" tabindex="-1" aria-labelledby="estModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1c2d42;">
                <h5 class="modal-title fw-bold" id="modalTitle">Registrar Establecimiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="estForm" action="{{ route('establecimientos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body bg-light p-4" style="max-height: 70vh; overflow-y: auto;">

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-building me-2"></i> Datos Administrativos e Hidráulicos
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Cuenta JAPAC *</label>
                                <input type="text" name="cuenta" id="input_cuenta" class="form-control rounded-1" data-label="NÚMERO DE CUENTA">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. de Medidor *</label>
                                <input type="text" name="num_medidor" id="input_num_medidor" class="form-control rounded-1" data-label="NÚMERO DE MEDIDOR">
                            </div>
                            <div class="col-md-4 d-flex align-items-center ps-4 pt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="empresa_nueva" id="input_empresa_nueva" value="1">
                                    <label class="form-check-label fw-bold text-info small text-uppercase" for="input_empresa_nueva">¿Es Empresa Nueva?</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nombre Comercial *</label>
                                <input type="text" name="nombre_establecimiento" id="input_nombre_establecimiento" class="form-control rounded-1" data-label="NOMBRE ESTABLECIMIENTO">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Razón Social *</label>
                                <input type="text" name="razon_social" id="input_razon_social" class="form-control rounded-1" data-label="RAZÓN SOCIAL">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">RFC *</label>
                                <input type="text" name="rfc" id="input_rfc" class="form-control text-uppercase font-monospace rounded-1" maxlength="15" data-label="RFC" oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Giro / Actividad *</label>
                                <input type="text" name="actividad" id="input_actividad" class="form-control rounded-1" placeholder="Ej: RESTAURANTE" data-label="ACTIVIDAD">
                            </div>
                            <div class="col-md-4" id="contenedor_status" style="display: none;">
                                <label class="form-label fw-bold small text-uppercase text-danger">Estatus Operativo *</label>
                                <select name="status" id="input_status" class="form-select border-danger rounded-1" data-label="STATUS">
                                    <option value="Activo">ACTIVO</option>
                                    <option value="Inactivo">INACTIVO</option>
                                    <option value="Clausurado">CLAUSURADO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-flask-vial me-2"></i> Infraestructura de Saneamiento y Permisos
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Trampas de Grasa (Cant)</label>
                                <input type="number" name="trampas_gra" id="input_trampas_gra" class="form-control rounded-1" min="0" data-label="TRAMPAS GRASA">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Trampas SST (Cant)</label>
                                <input type="number" name="trampas_sst" id="input_trampas_sst" class="form-control rounded-1" min="0" data-label="TRAMPAS SST">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">No. Permiso Descarga</label>
                                <input type="text" name="num_permiso" id="input_num_permiso" class="form-control rounded-1" data-label="NÚMERO DE PERMISO">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Emisión Permiso</label>
                                <input type="date" name="fechaemision_permiso" id="input_fechaemision_permiso" class="form-control rounded-1" data-label="FECHA EMISIÓN">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-address-book me-2"></i> Contacto Corporativo
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Teléfono de Enlace *</label>
                                <input type="text" name="telefono" id="input_telefono" class="form-control rounded-1" data-label="TELÉFONO">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Correo Electrónico *</label>
                                <input type="email" name="correo_electronico" id="input_correo_electronico" class="form-control rounded-1" data-label="CORREO ELECTRÓNICO">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-map-location-dot me-2"></i> Ubicación Física
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Código Postal *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-1"><i class="fa-solid fa-map-pin"></i></span>
                                    <input type="text" name="codigo_postal" id="input_codigo_postal" class="form-control font-monospace" maxlength="5" placeholder="Ej: 80000" onkeyup="buscarCodigoPostal(this.value)" data-label="CÓDIGO POSTAL">
                                    <span class="input-group-text bg-white rounded-end-1" id="cp_loader" style="display: none;">
                                        <i class="fa-solid fa-spinner fa-spin" style="color: #0056b3;"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Estado</label>
                                <input type="text" id="input_estado" class="form-control bg-light rounded-1" readonly placeholder="Sinaloa">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Municipio</label>
                                <input type="text" id="input_municipio" class="form-control bg-light rounded-1" readonly placeholder="Culiacán">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Colonia *</label>
                                <select name="colonia" id="input_colonia" class="form-select rounded-1" disabled data-label="COLONIA">
                                    <option value="">Ingrese un C.P. válido...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Calle *</label>
                                <input type="text" name="calle" id="input_calle" class="form-control rounded-1" data-label="CALLE">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-muted">Num. Ext *</label>
                                <input type="number" name="num_exterior" id="input_num_exterior" class="form-control rounded-1" data-label="NUMERO EXTERIOR">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-muted">Num. Int</label>
                                <input type="text" name="num_interior" id="input_num_interior" class="form-control rounded-1" data-label="NUMERO INTERIOR">
                            </div>
                            <div class="col-md-10">
                                <label class="form-label fw-bold small text-uppercase text-muted">Observaciones de Campo</label>
                                <input type="text" name="observaciones" id="input_observaciones" class="form-control rounded-1" placeholder="Detalles extra del predio..." data-label="OBSERVACIONES">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-1 px-4 font-weight-bold shadow-sm" style="background-color: #0056b3;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let debounceTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({ title: '¡Completado!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0056b3' });
        @endif
    });

    document.getElementById('estForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const camposAValidar = [
            'input_cuenta', 'input_num_medidor', 'input_nombre_establecimiento',
            'input_razon_social', 'input_rfc', 'input_actividad', 'input_telefono',
            'input_correo_electronico', 'input_codigo_postal', 'input_colonia', 'input_calle', 'input_num_exterior'
        ];

        for (let id of camposAValidar) {
            const elemento = document.getElementById(id);
            if (elemento) {
                if (!elemento.value.trim()) {
                    Swal.fire({
                        title: 'Falta información',
                        text: `El campo ${elemento.getAttribute('data-label')} es obligatorio.`,
                        icon: 'warning',
                        confirmButtonColor: '#0056b3'
                    });
                    return false;
                }
            }
        }

        document.getElementById('input_colonia').removeAttribute('disabled');
        this.submit();
    });

    function prepararAlta() {
        const formulario = document.getElementById('estForm');
        formulario.reset();
        formulario.action = "{{ route('establecimientos.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-circle-plus me-2 text-info"></i> Registrar Establecimiento';
        document.getElementById('contenedor_status').style.display = 'none';
        document.getElementById('input_empresa_nueva').checked = false;
        document.getElementById('input_colonia').setAttribute('disabled', 'disabled');
    }

    function prepararEdicion(est) {
        prepararAlta();
        const formulario = document.getElementById('estForm');
        formulario.action = `/establecimientos/${est.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerHTML = `<i class="fa-solid fa-pen-to-square me-2 text-warning"></i> Editar: ${est.nombre_establecimiento}`;

        document.getElementById('input_cuenta').value = est.cuenta;
        document.getElementById('input_num_medidor').value = est.num_medidor;
        document.getElementById('input_empresa_nueva').checked = est.empresa_nueva;
        document.getElementById('input_nombre_establecimiento').value = est.nombre_establecimiento;
        document.getElementById('input_razon_social').value = est.razon_social;
        document.getElementById('input_rfc').value = est.rfc;
        document.getElementById('input_actividad').value = est.actividad;
        document.getElementById('input_trampas_gra').value = est.trampas_gra || '';
        document.getElementById('input_trampas_sst').value = est.trampas_sst || '';
        document.getElementById('input_num_permiso').value = est.num_permiso || '';

        if(est.fechaemision_permiso) document.getElementById('input_fechaemision_permiso').value = est.fechaemision_permiso.substring(0,10);

        document.getElementById('input_telefono').value = est.telefono;
        document.getElementById('input_correo_electronico').value = est.correo_electronico;
        document.getElementById('input_calle').value = est.calle;
        document.getElementById('input_num_exterior').value = est.num_exterior;
        document.getElementById('input_num_interior').value = est.num_interior || '';
        document.getElementById('input_observaciones').value = est.observaciones || '';
        document.getElementById('input_codigo_postal').value = est.codigo_postal;

        if (est.codigo_postal) buscarCodigoPostal(est.codigo_postal, est.colonia);

        document.getElementById('input_status').value = est.status || 'Activo';
        document.getElementById('contenedor_status').style.display = 'block';

        const myModal = new bootstrap.Modal(document.getElementById('estModal'));
        myModal.show();
    }

    function buscarCodigoPostal(cp, coloniaPreseleccionada = null) {
        clearTimeout(debounceTimeout);
        if (cp.length !== 5 || isNaN(cp)) return;

        debounceTimeout = setTimeout(() => {
            document.getElementById('cp_loader').style.display = 'block';
            fetch(`/api/codigo-postal/${cp}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cp_loader').style.display = 'none';
                    if (data.exito) {
                        document.getElementById('input_estado').value = data.estado.toUpperCase();
                        document.getElementById('input_municipio').value = data.municipio.toUpperCase();
                        const sc = document.getElementById('input_colonia');
                        sc.innerHTML = '<option value="">Seleccione una colonia...</option>';
                        data.colonias.forEach(col => {
                            const op = document.createElement('option');
                            op.value = col.toUpperCase(); op.textContent = col.toUpperCase();
                            sc.appendChild(op);
                        });
                        sc.removeAttribute('disabled');
                        if (coloniaPreseleccionada) sc.value = coloniaPreseleccionada.toUpperCase();
                    }
                });
        }, 150);
    }

    function confirmarBaja(form) {
        Swal.fire({
            title: '¿Suspender establecimiento?',
            text: "Se cambiará su estado a Inactivo dentro del control de descargas.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, suspender',
            cancelButtonText: 'Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    }
</script>
@endsection
