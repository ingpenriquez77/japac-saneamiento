@extends('layouts.app')

@section('title', 'Control de Usuarios')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Gestión de Personal / Usuarios</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px; letter-spacing: 0.5px;">
            <i class="fas fa-shield-alt me-1"></i> Administrador Global
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px; letter-spacing: 0.3px;" data-bs-toggle="modal" data-bs-target="#userModal" onclick="prepararAlta()">
        <i class="fa-solid fa-user-plus me-2"></i> Registrar Nuevo Usuario
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-user-tie me-2" style="color: #0056b3;"></i> Plantilla de Usuarios del Sistema
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3">No. / Nombre</th>
                        <th class="py-3">Puesto / Área</th>
                        <th class="py-3">CURP / NSS</th>
                        <th class="py-3">Contacto e Identidad</th>
                        <th class="text-center py-3" style="width: 8%;">Estado</th>
                        <th class="text-center py-3" style="width: 15%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr class="border-bottom">

                        <td class="ps-4 py-3">
                            <span class="badge bg-dark mb-1 text-uppercase" style="font-size: 10px; font-weight: 700;">USR-{{ str_pad($user->id, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="fw-bold d-block text-dark" style="font-size: 14.5px;">{{ $user->nombre }} {{ $user->paterno }} {{ $user->materno }}</span>
                            <small class="text-muted" style="font-size: 12px;">
                                <i class="fa-solid fa-cake-candles me-1"></i> Nacimiento: {{ $user->fechanacimiento ? \Carbon\Carbon::parse($user->fechanacimiento)->format('d/m/Y') : 'No registrada' }}
                            </small>
                        </td>

                        <td class="py-3">
                            <span class="fw-bold text-uppercase d-block" style="color: #0056b3; font-size: 13.5px;">{{ $user->puesto->nombre ?? 'Sin Puesto' }}</span>
                            <small class="badge bg-light text-dark border rounded-1 text-uppercase" style="font-size: 11px;">
                                <i class="fa-solid fa-briefcase me-1 text-secondary"></i> {{ $user->departamento->nombre ?? 'Sin Área' }}
                            </small>
                        </td>

                        <td class="py-3">
                            <div class="mb-1 small">
                                <span class="text-muted fw-bold">CURP:</span>
                                <code class="text-dark font-monospace" style="font-size: 12px;">{{ $user->curp ?? 'N/A' }}</code>
                            </div>
                            <div class="small">
                                <span class="text-muted fw-bold">NSS:</span>
                                <span class="text-secondary font-monospace" style="font-size: 12px;">{{ $user->nss ?? 'N/A' }}</span>
                            </div>
                        </td>

                        <td class="py-3">
                            <div class="small mb-1 text-dark fw-bold">
                                <i class="fa-solid fa-at text-silver me-1"></i>{{ $user->usuario }}
                            </div>
                            <div class="small mb-1 text-dark">
                                <i class="fa-solid fa-envelope me-1 text-primary"></i> {{ $user->email }}
                            </div>
                            @if($user->telefono)
                            <div class="small mb-1 text-dark" style="font-size: 12.5px;">
                                <i class="fa-solid fa-phone me-1 text-success"></i> {{ $user->telefono }} <span class="text-muted">({{ $user->tipo_telefono }})</span>
                            </div>
                            @endif
                            @if($user->calle)
                                <small class="text-muted d-block text-truncate" style="max-width: 240px; font-size: 12px;">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    {{ $user->calle }} #{{ $user->numerocasa }}, Col. {{ $user->colonia }}
                                </small>
                            @endif
                        </td>

                        <td class="text-center py-3">
                            @if(($user->estado_operativo ?? 'Activo') === 'Activo')
                                <span class="badge bg-success px-3 py-1 rounded-1 text-uppercase" style="font-size: 11px; font-weight: 700;">Activo</span>
                            @else
                                <span class="badge bg-danger px-3 py-1 rounded-1 text-uppercase" style="font-size: 11px; font-weight: 700;">Baja</span>
                            @endif
                        </td>

                        <td class="text-center py-3">
                            <div class="d-inline-flex gap-1 justify-content-center w-100">
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm px-2 py-1 rounded-1 d-flex align-items-center" style="font-size: 12px; font-weight: 600;" onclick="prepararEdicion({{ json_encode($user) }})">
                                    <i class="fa-solid fa-user-pen me-1"></i> Editar
                                </button>

                                @if($user->usuario !== 'admin' && ($user->estado_operativo ?? 'Activo') === 'Activo')
                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmarBaja(this.form)" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 d-flex align-items-center" style="font-size: 12px; font-weight: 600;">
                                        <i class="fa-solid fa-user-minus me-1"></i> Baja
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1c2d42;">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="fa-solid fa-user-tie me-2 text-info"></i> Registrar Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="userForm" action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body bg-light p-4" style="max-height: 70vh; overflow-y: auto;">

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-id-card me-2"></i> Información Personal Base
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">ID Usuario / Alias *</label>
                                <input type="text" name="usuario" id="input_usuario" class="form-control rounded-1" placeholder="Ej: pedro.enriquez" data-label="ID USUARIO / ALIAS">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nombre(s) *</label>
                                <input type="text" name="nombre" id="input_nombre" class="form-control rounded-1" data-label="NOMBRE">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Apellido Paterno *</label>
                                <input type="text" name="paterno" id="input_paterno" class="form-control rounded-1" data-label="APELLIDO PATERNO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Apellido Materno</label>
                                <input type="text" name="materno" id="input_materno" class="form-control rounded-1" data-label="APELLIDO MATERNO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Sexo *</label>
                                <select name="sexo" id="input_sexo" class="form-select rounded-1" data-label="SEXO">
                                    <option value="">Seleccione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Fecha de Nacimiento *</label>
                                <input type="date" name="fechanacimiento" id="input_fechanacimiento" class="form-control rounded-1" data-label="FECHA DE NACIMIENTO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase" style="color: #0056b3;"><i class="fa-solid fa-map-location-dot me-1"></i> Edo. Nacimiento *</label>
                                <select name="lugar_nacimiento" id="input_lugar_nacimiento" class="form-select rounded-1" style="border-color: #0056b3;" data-label="ESTADO DE NACIMIENTO">
                                    <option value="">Seleccione estado...</option>
                                    <option value="SINALOA">SINALOA</option>
                                    <option value="SONORA">SONORA</option>
                                    <option value="BAJA CALIFORNIA">BAJA CALIFORNIA</option>
                                    <option value="CHIHUAHUA">CHIHUAHUA</option>
                                    <option value="DURANGO">DURANGO</option>
                                    <option value="NAYARIT">NAYARIT</option>
                                    <option value="JALISCO">JALISCO</option>
                                    <option value="CIUDAD DE MÉXICO">CIUDAD DE MÉXICO</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase" style="color: #0056b3;"><i class="fa-solid fa-fingerprint me-1"></i> CURP *</label>
                                <input type="text" name="curp" id="input_curp" class="form-control text-uppercase font-monospace rounded-1" style="border-color: #0056b3;" maxlength="18" placeholder="ABCD123456XYZ..." data-label="CURP" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                            </div>
                            <div class="col-md-12" id="contenedor_password">
                                <label class="form-label fw-bold small text-uppercase text-muted">Contraseña * <span class="text-danger text-xs" id="pass_help">(Obligatoria para altas)</span></label>
                                <input type="password" name="password" id="input_password" class="form-control rounded-1" placeholder="Mínimo 6 caracteres" data-label="CONTRASEÑA">
                            </div>
                            <div class="col-md-12" id="contenedor_estado_operativo" style="display: none;">
                                <label class="form-label fw-bold small text-uppercase text-danger">Estado Operativo *</label>
                                <select name="estado_operativo" id="input_estado_operativo" class="form-select border-danger rounded-1" data-label="ESTADO OPERATIVO">
                                    <option value="Activo">ACTIVO / OPERATIVO</option>
                                    <option value="Baja">BAJA LÓGICA</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border mb-4">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-shield-halved me-2"></i> Puesto, Contacto y Seguridad Social
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Área / Departamento *</label>
                                <select name="departamento_id" id="input_departamento_id" class="form-select rounded-1" data-label="ÁREA / DEPARTAMENTO" onchange="filtrarPuestosPorDepartamento(this.value)">
                                    <option value="">Seleccione área...</option>
                                    @foreach($departamentos ?? \App\Models\Departamento::all() as $dep)
                                        @if($dep->codigo !== 'ADM-GEN')
                                            <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Puesto Asignado *</label>
                                <select name="puesto_id" id="input_puesto_id" class="form-select rounded-1" data-label="PUESTO ASIGNADO" disabled>
                                    <option value="">Seleccione primero un área...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Correo Electrónico *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-1"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" name="email" id="input_email" class="form-control rounded-end-1" placeholder="ejemplo@japac.gob.mx" data-label="CORREO ELECTRÓNICO">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Número de Teléfono *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-1"><i class="fa-solid fa-phone"></i></span>
                                    <input type="text" name="telefono" id="input_telefono" class="form-control rounded-end-1" placeholder="6671234567" maxlength="15" data-label="NÚMERO DE TELÉFONO">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tipo *</label>
                                <select name="tipo_telefono" id="input_tipo_telefono" class="form-select rounded-1" data-label="TIPO DE TELÉFONO">
                                    <option value="CELULAR">CELULAR</option>
                                    <option value="CASA">CASA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">NSS (11 Caracteres)</label>
                                <input type="text" name="nss" id="input_nss" class="form-control font-monospace rounded-1" maxlength="11" placeholder="12345678901" data-label="NSS">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2" style="color: #0056b3;">
                            <i class="fa-solid fa-house-user me-2"></i> Localización / Domicilio
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Código Postal *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-1"><i class="fa-solid fa-map-pin"></i></span>
                                    <input type="text" name="codigopostal" id="input_codigopostal" class="form-control font-monospace" maxlength="5" placeholder="Ej: 80000" onkeyup="buscarCodigoPostal(this.value)" data-label="CÓDIGO POSTAL">
                                    <span class="input-group-text bg-white rounded-end-1" id="cp_loader" style="display: none;">
                                        <i class="fa-solid fa-spinner fa-spin" style="color: #0056b3;"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Estado *</label>
                                <input type="text" name="estado" id="input_estado" class="form-control bg-light rounded-1" readonly placeholder="Automático" data-label="ESTADO">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Municipio *</label>
                                <input type="text" name="municipio" id="input_municipio" class="form-control bg-light rounded-1" readonly placeholder="Automático" data-label="MUNICIPIO">
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
                                <input type="text" name="numerocasa" id="input_numerocasa" class="form-control rounded-1" data-label="NÚMERO EXTERIOR">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-1 px-4 font-weight-bold shadow-sm" style="background-color: #0056b3;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Usuario
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
            Swal.fire({ title: '¡Operación Exitosa!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0056b3' });
        @endif
        @if(session('error'))
            Swal.fire({ title: 'Error detectado', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#dc3545' });
        @endif
    });

    // 🚀 NUEVA FUNCIÓN DE FILTRADO OPTIMIZADA (NATIVA SIN CLONACIÓN)
    function filtrarPuestosPorDepartamento(deptoId, puestoPreseleccionado = null) {
        const selectPuesto = document.getElementById('input_puesto_id');

        if (!deptoId) {
            selectPuesto.value = "";
            selectPuesto.setAttribute('disabled', 'disabled');
            selectPuesto.innerHTML = '<option value="">Seleccione primero un área...</option>';
            return;
        }

        // 1. Array de objetos estructurado dinámicamente desde Blade (Omitiendo accesos raíz)
        const todosLosPuestos = [
            @foreach(\App\Models\Puesto::where('nivel_acceso', '!=', 'admin')->get() as $p)
                { id: "{{ $p->id }}", nombre: "{{ $p->nombre }}", depto_id: "{{ $p->departamento_id }}" },
            @endforeach
        ];

        // 2. Filtrado lógico
        const puestosFiltrados = todosLosPuestos.filter(p => p.depto_id == deptoId);

        // 3. Renderizado del select
        if (puestosFiltrados.length === 0) {
            selectPuesto.innerHTML = '<option value="">No hay puestos en este departamento...</option>';
            selectPuesto.setAttribute('disabled', 'disabled');
            return;
        }

        selectPuesto.removeAttribute('disabled');
        let htmlOpciones = '<option value="">Seleccione puesto...</option>';

        puestosFiltrados.forEach(p => {
            htmlOpciones += `<option value="${p.id}">${p.nombre.toUpperCase()}</option>`;
        });

        selectPuesto.innerHTML = htmlOpciones;

        // 4. Inyección en modo edición
        if (puestoPreseleccionado) {
            selectPuesto.value = puestoPreseleccionado;
        }
    }

    // Interceptor Frontend
    document.getElementById('userForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const camposAValidar = [
            'input_usuario', 'input_nombre', 'input_paterno', 'input_sexo',
            'input_fechanacimiento', 'input_lugar_nacimiento', 'input_curp',
            'input_departamento_id', 'input_puesto_id', 'input_email', 'input_telefono',
            'input_codigopostal', 'input_estado', 'input_municipio', 'input_colonia', 'input_calle', 'input_numerocasa'
        ];

        if (document.getElementById('formMethod').value === 'POST') {
            camposAValidar.push('input_password');
        }

        for (let id of camposAValidar) {
            const elemento = document.getElementById(id);
            if (elemento) {
                const valor = elemento.value.trim();
                const nombreCampo = elemento.getAttribute('data-label') || 'ESTE CAMPO';

                if (id === 'input_colonia' && elemento.disabled) {
                    Swal.fire({ title: 'Código Postal Requerido', text: 'Por favor introduce un Código Postal válido.', icon: 'warning', confirmButtonColor: '#0056b3' });
                    return false;
                }

                if (!valor || valor === "") {
                    Swal.fire({ title: 'Campo Obligatorio Faltante', text: `Por favor, ingresa o selecciona: ${nombreCampo}.`, icon: 'warning', confirmButtonColor: '#0056b3' }).then(() => {
                        setTimeout(() => elemento.focus(), 250);
                    });
                    return false;
                }
            }
        }

        document.getElementById('input_colonia').removeAttribute('disabled');
        this.submit();
    });

    function prepararAlta() {
        const formulario = document.getElementById('userForm');
        formulario.reset();

        formulario.action = "{{ route('usuarios.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-user-plus me-2 text-info"></i> Registrar Nuevo Usuario';

        document.getElementById('input_usuario').readOnly = false;
        document.getElementById('contenedor_estado_operativo').style.display = 'none';
        document.getElementById('pass_help').textContent = "(Obligatoria para altas)";

        document.getElementById('input_colonia').innerHTML = '<option value="">Ingrese un C.P. válido...</option>';
        document.getElementById('input_colonia').setAttribute('disabled', 'disabled');

        // Inicializar el select de puestos cerrado de forma segura
        filtrarPuestosPorDepartamento("");
    }

    function prepararEdicion(user) {
        const formulario = document.getElementById('userForm');
        formulario.reset();

        formulario.action = `/usuarios/${user.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').innerHTML = `<i class="fa-solid fa-user-pen me-2 text-warning"></i> Editar Datos: ${user.nombre}`;

        document.getElementById('input_usuario').value = user.usuario;
        document.getElementById('input_usuario').readOnly = true;

        document.getElementById('input_nombre').value = user.nombre;
        document.getElementById('input_paterno').value = user.paterno;
        document.getElementById('input_materno').value = user.materno || '';
        document.getElementById('input_sexo').value = user.sexo || '';

        if(user.fechanacimiento) document.getElementById('input_fechanacimiento').value = user.fechanacimiento.substring(0, 10);

        document.getElementById('input_lugar_nacimiento').value = user.lugar_nacimiento || '';
        document.getElementById('input_curp').value = user.curp || '';

        // 🚀 Forzar carga secuencial de la relación estructurada
        document.getElementById('input_departamento_id').value = user.departamento_id || '';
        filtrarPuestosPorDepartamento(user.departamento_id, user.puesto_id);

        document.getElementById('input_email').value = user.email;
        document.getElementById('input_telefono').value = user.telefono || '';
        document.getElementById('input_tipo_telefono').value = user.tipo_telefono || 'CELULAR';
        document.getElementById('input_nss').value = user.nss || '';
        document.getElementById('input_calle').value = user.calle || '';
        document.getElementById('input_numerocasa').value = user.numerocasa || '';
        document.getElementById('input_estado').value = user.estado || '';
        document.getElementById('input_municipio').value = user.municipio || '';
        document.getElementById('input_codigopostal').value = user.codigopostal || '';

        if (user.codigopostal) buscarCodigoPostal(user.codigopostal, user.colonia);

        document.getElementById('pass_help').textContent = "(Vacío para conservar actual)";
        document.getElementById('input_estado_operativo').value = user.estado_operativo || 'Activo';
        document.getElementById('contenedor_estado_operativo').style.display = 'block';

        const myModal = new bootstrap.Modal(document.getElementById('userModal'));
        myModal.show();
    }

    function buscarCodigoPostal(cp, coloniaPreseleccionada = null) {
        clearTimeout(debounceTimeout);
        const inputEstado = document.getElementById('input_estado');
        const inputMunicipio = document.getElementById('input_municipio');
        const selectColonia = document.getElementById('input_colonia');
        const loader = document.getElementById('cp_loader');

        if (cp.length !== 5 || isNaN(cp)) return;

        debounceTimeout = setTimeout(() => {
            if(loader) loader.style.display = 'block';

            fetch(`/api/codigo-postal/${cp}`)
                .then(response => response.json())
                .then(data => {
                    if(loader) loader.style.display = 'none';
                    if (data.exito) {
                        inputEstado.value = data.estado.toUpperCase();
                        inputMunicipio.value = data.municipio.toUpperCase();
                        selectColonia.innerHTML = '<option value="">Seleccione una colonia...</option>';
                        data.colonias.forEach(colonia => {
                            const option = document.createElement('option');
                            option.value = colonia.toUpperCase();
                            option.textContent = colonia.toUpperCase();
                            selectColonia.appendChild(option);
                        });
                        selectColonia.removeAttribute('disabled');
                        if (coloniaPreseleccionada) selectColonia.value = coloniaPreseleccionada.toUpperCase();
                    }
                });
        }, 150);
    }

    function confirmarBaja(form) {
        Swal.fire({
            title: '¿Dar de baja al usuario?',
            text: "Esta acción restringirá sus accesos al sistema de saneamiento.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, dar de baja',
            cancelButtonText: 'Cancelar'
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    }
</script>
@endsection
