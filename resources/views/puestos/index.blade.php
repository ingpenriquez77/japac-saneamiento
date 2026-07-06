@extends('layouts.app')

@section('title', 'Control de Puestos')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Puestos y Perfiles Operativos</h2>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#puestoModal" onclick="prepararAlta()">
        <i class="fa-solid fa-user-gear me-2"></i> Nuevo Puesto
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                <tr>
                    <th class="ps-4 py-3">Nombre del Puesto</th>
                    <th class="py-3">Departamento / Área Asignada</th>
                    <th class="py-3">Nivel de Acceso Técnico</th>
                    <th class="py-3 text-center" style="width: 15%;">Plazas Asignadas</th>
                    <th class="text-center py-3" style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($puestos as $puesto)
                <tr class="border-bottom">
                    <td class="ps-4 py-3 text-dark fw-bold text-uppercase" style="font-size: 13.5px;">{{ $puesto->nombre }}</td>
                    <td class="py-3">
                        @if(($puesto->departamento->codigo ?? '') === 'ADM-GEN')
                            <span class="badge bg-dark text-white rounded-1 text-uppercase">
                                <i class="fa-solid fa-shield-alt me-1 text-warning"></i> {{ $puesto->departamento->nombre }}
                            </span>
                        @else
                            <span class="badge bg-light text-dark border rounded-1 text-uppercase">
                                <i class="fa-solid fa-briefcase me-1 text-secondary"></i> {{ $puesto->departamento->nombre ?? 'Sin Área Asignada' }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($puesto->nivel_acceso === 'admin')
                            <code class="bg-dark text-warning px-2 py-1 rounded font-monospace fw-bold" style="font-size: 12px;">ROOT / {{ $puesto->nivel_acceso }}</code>
                        @else
                            <code class="bg-light text-secondary px-2 py-1 rounded font-monospace fw-bold">{{ $puesto->nivel_acceso }}</code>
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge bg-dark font-monospace px-2 py-1">{{ $puesto->users_count }} Empleados</span>
                    </td>
                    <td class="text-center py-3">
                        <div class="d-inline-flex gap-1 justify-content-center">
                            @if($puesto->nivel_acceso === 'admin')
                                <span class="text-muted small font-italic"><i class="fa-solid fa-lock me-1 text-danger"></i>Protegido</span>
                            @else
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm rounded-1" onclick="prepararEdicion({{ json_encode($puesto) }})">
                                    <i class="fa-solid fa-user-pen"></i> Editar
                                </button>
                                <form action="{{ route('puestos.destroy', $puesto->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Remover este rol de la plantilla?');" class="btn btn-sm btn-danger shadow-sm rounded-1">
                                        <i class="fa-solid fa-user-minus"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No hay puestos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="puestoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1c2d42;">
                <h5 class="modal-title fw-bold" id="modalTitle">Configurar Puesto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="puestoForm" action="{{ route('puestos.store') }}" method="POST">
                @csrf <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body bg-white p-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Departamento / Adscripción *</label>
                        <select name="departamento_id" id="input_departamento_id" class="form-select" required>
                            <option value="">Selecciona el área...</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->nombre }} ({{ $dep->codigo }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Puesto *</label>
                        <input type="text" name="nombre" id="input_nombre" class="form-control" placeholder="Ej: Inspector Técnico de Descargas" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Nivel de Acceso (System Flag) *</label>
                        <select name="nivel_acceso" id="input_nivel_acceso" class="form-select" required>
                            <option value="operador">Operador Regular / Consultas</option>
                            <option value="gerente">Gerente de Área / Validaciones</option>
                            <option value="sistemasIT">Sistemas / Soporte Técnico</option>
                            </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-1 px-4" style="background-color: #0056b3;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success')) Swal.fire({ title: '¡Completado!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0056b3' }); @endif
        @if(session('error')) Swal.fire({ title: 'Operación Restringida', text: "{{ session('error') }}", icon: 'error', confirmButtonColor: '#dc3545' }); @endif
    });

    function prepararAlta() {
        document.getElementById('puestoForm').reset();
        document.getElementById('puestoForm').action = "{{ route('puestos.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = 'Configurar Nuevo Puesto';
    }

    function prepararEdicion(puesto) {
        prepararAlta();
        document.getElementById('puestoForm').action = `/puestos/${puesto.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'Editar Atributos de Puesto';

        document.getElementById('input_departamento_id').value = puesto.departamento_id || '';
        document.getElementById('input_nombre').value = puesto.nombre;
        document.getElementById('input_nivel_acceso').value = puesto.nivel_acceso;

        (new bootstrap.Modal(document.getElementById('puestoModal'))).show();
    }
</script>
@endsection
