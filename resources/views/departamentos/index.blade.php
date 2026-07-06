@extends('layouts.app')

@section('title', 'Control de Departamentos')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Departamentos / Áreas JAPAC</h2>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#depModal" onclick="prepararAlta()">
        <i class="fa-solid fa-folder-plus me-2"></i> Nueva Área
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                <tr>
                    <th class="ps-4 py-3" style="width: 15%;">Código Identificador</th>
                    <th class="py-3">Nombre del Departamento / Área</th>
                    <th class="py-3 text-center" style="width: 20%;">Personal Adscrito</th>
                    <th class="text-center py-3" style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departamentos as $dep)
                <tr class="border-bottom">
                    <td class="ps-4 py-3 font-monospace fw-bold text-primary">{{ $dep->codigo }}</td>
                    <td class="py-3 text-dark fw-semibold">
                        @if($dep->codigo === 'ADM-GEN')
                            <i class="fa-solid fa-shield-alt text-warning me-1"></i>
                        @endif
                        {{ $dep->nombre }}
                    </td>
                    <td class="py-3 text-center">
                        <span class="badge bg-secondary font-monospace px-2 py-1">{{ $dep->users_count }} Integrantes</span>
                    </td>
                    <td class="text-center py-3">
                        <div class="d-inline-flex gap-1 justify-content-center">
                            @if($dep->codigo === 'ADM-GEN')
                                <span class="text-muted small font-italic"><i class="fa-solid fa-lock me-1 text-danger"></i>Protegido</span>
                            @else
                                <button type="button" class="btn btn-sm btn-info text-white shadow-sm rounded-1" onclick="prepararEdicion({{ json_encode($dep) }})">
                                    <i class="fa-solid fa-pen"></i> Editar
                                </button>
                                <form action="{{ route('departamentos.destroy', $dep->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Remover este departamento corporativo?');" class="btn btn-sm btn-danger shadow-sm rounded-1">
                                        <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No hay áreas configuradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="depModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header text-white" style="background-color: #1c2d42;">
                <h5 class="modal-title fw-bold" id="modalTitle">Registrar Área</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="depForm" action="{{ route('departamentos.store') }}" method="POST">
                @csrf <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body bg-white p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Código Interno (Ej: SAN) *</label>
                        <input type="text" name="codigo" id="input_codigo" class="form-control text-uppercase" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Departamento *</label>
                        <input type="text" name="nombre" id="input_nombre" class="form-control" required>
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
        document.getElementById('depForm').reset();
        document.getElementById('depForm').action = "{{ route('departamentos.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = 'Registrar Nueva Área';
    }
    function prepararEdicion(dep) {
        prepararAlta();
        document.getElementById('depForm').action = `/departamentos/${dep.id}`;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'Editar Departamento';
        document.getElementById('input_codigo').value = dep.codigo;
        document.getElementById('input_nombre').value = dep.nombre;
        (new bootstrap.Modal(document.getElementById('depModal'))).show();
    }
</script>
@endsection
