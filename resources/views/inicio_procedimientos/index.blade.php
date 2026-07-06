@extends('layouts.app')

@section('title', 'Inicio de Procedimiento')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Inicio de Procedimiento</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-gavel me-1"></i> Área Contenciosa / Saneamiento JAPAC
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #0056b3; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#procModal">
        <i class="fa-solid fa-gavel me-2"></i> Radicar Inicio de Procedimiento
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #0056b3 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-scale-balanced me-2 text-primary"></i> Juicios y Procedimientos en Curso
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3">Oficio Procedimiento</th>
                        <th class="py-3">Establecimiento Regulado</th>
                        <th class="py-3">Visita de Origen</th>
                        <th class="py-3">Fecha Notificación</th>
                        <th class="py-3">Plazo Legal</th>
                        <th class="text-center py-3">Estatus</th>
                        <th class="text-center py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procedimientos as $p)
                    <tr class="border-bottom">
                        <td class="ps-4 py-3 font-monospace fw-bold text-primary">{{ $p->num_oficio_inicio }}</td>
                        <td>
                            <span class="fw-bold d-block text-dark">{{ $p->visita->establecimiento->nombre_establecimiento }}</span>
                            <small class="text-muted font-monospace">RFC: {{ $p->visita->establecimiento->rfc }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $p->visita->num_oficioVI }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->fecha_notificacion)->format('d/m/Y') }}</td>
                        <td><span class="text-danger fw-bold"><i class="fa-solid fa-clock me-1"></i>{{ $p->plazo_concedido }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-info text-white rounded-1 text-uppercase" style="font-size: 10.5px;">{{ $p->status }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('inicio_procedimientos.pdf', $p->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> Notificación PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-scale-unbalanced fa-2x mb-2 d-block opacity-50"></i> No hay expedientes contenciosos radicados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="procModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-gavel me-2 text-warning"></i> Apertura de Procedimiento Administrativo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inicio_procedimientos.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-light p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-uppercase text-muted">Vincular a Orden de Visita Previa *</label>
                            <select name="visita_inspeccion_id" class="form-select rounded-1" required>
                                <option value="">-- Seleccione la Orden de Origen JAPAC --</option>
                                @foreach($visitas as $v)
                                    <option value="{{ $v->id }}">{{ $v->num_oficioVI }} - {{ $v->establecimiento->nombre_establecimiento }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-uppercase text-muted">No. Oficio de Inicio (Contencioso) *</label>
                            <input type="text" name="num_oficio_inicio" class="form-control font-monospace fw-bold" placeholder="Ej: No.: D.J. 015/17" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha de la Notificación Legal *</label>
                            <input type="date" name="fecha_notificacion" class="form-control rounded-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Término / Plazo Concedido *</label>
                            <input type="text" name="plazo_concedido" class="form-control rounded-1" value="5 días hábiles" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Fundamentación Jurídica *</label>
                        <input type="text" name="fundamento_legal" class="form-control rounded-1 border-primary" value="Punto 8.2 del Régimen Tarifario Vigente de JAPAC y Ley de Agua Potable del Estado de Sinaloa" required>
                    </div>

                    <div>
                        <label class="form-label fw-bold small text-uppercase text-muted">Hechos y Agravios que la Motivan (Dictamen) *</label>
                        <textarea name="hechos_motivo" rows="4" class="form-control rounded-1" placeholder="Asiente de forma fundada el porqué se inicia el juicio formal debido a las irregularidades o desacatos..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-1 px-4 text-white fw-bold shadow-sm" style="background-color: #0056b3;">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Radicar e Imprimir Emisión
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
            Swal.fire({ title: '¡Radicado!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#0056b3' });
        @endif
    });
</script>
@endsection
