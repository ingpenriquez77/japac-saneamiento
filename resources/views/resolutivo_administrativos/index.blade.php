@extends('layouts.app')

@section('title', 'Resolutivo Administrativo')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Resolutivo Administrativo</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-gavel me-1"></i> Sentencias y Resoluciones / JAPAC
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #dc3545; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#resModal">
        <i class="fa-solid fa-gavel me-2"></i> Emitir Resolutivo Final
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #dc3545 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-gavel me-2 text-danger"></i> Historial de Sentencias Ejecutadas
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                <thead class="bg-light text-secondary fw-bold" style="font-size: 13px;">
                    <tr>
                        <th class="ps-4 py-3">No. Resolutivo</th>
                        <th class="py-3">Establecimiento</th>
                        <th class="py-3">Juicio de Origen</th>
                        <th class="py-3">Fecha Dictamen</th>
                        <th class="text-end py-3">Multa Aplicada</th>
                        <th class="text-center py-3">Estatus</th>
                        <th class="text-center py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resolutivos as $r)
                    <tr class="border-bottom">
                        <td class="ps-4 py-3 font-monospace fw-bold text-danger">{{ $r->num_resolutivo }}</td>
                        <td>
                            <span class="fw-bold d-block text-dark">{{ $r->inicioProcedimiento->visita->establecimiento->nombre_establecimiento }}</span>
                            <small class="text-muted">Acción: {{ $r->sancion_adicional ?? 'Ninguna' }}</small>
                        </td>
                        <td><span class="badge bg-secondary font-monospace">{{ $r->inicioProcedimiento->num_oficio_inicio }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($r->fecha_resolucion)->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold text-danger pe-4">${{ number_format($r->monto_sancion_pesos, 2) }} M.N.</td>
                        <td class="text-center">
                            <span class="badge bg-danger text-white rounded-1 text-uppercase" style="font-size: 10.5px;">{{ $r->status_final }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('resolutivo_administrativos.pdf', $r->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> Ver Sentencia PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-gavel fa-2x mb-2 d-block opacity-50"></i> No se han emitido resoluciones de finiquito.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="resModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-gavel me-2 text-warning"></i> Resolución Administrativa de Saneamiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('resolutivo_administrativos.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-light p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-uppercase text-muted">Vincular Juicio de Inicio Previo *</label>
                            <select name="inicio_procedimiento_id" class="form-select rounded-1" required>
                                <option value="">-- Seleccione el Oficio Litigante --</option>
                                @foreach($procedimientos as $p)
                                    <option value="{{ $p->id }}">{{ $p->num_oficio_inicio }} - {{ $p->visita->establecimiento->nombre_establecimiento }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-uppercase text-muted">No. Resolutivo Oficial *</label>
                            <input type="text" name="num_resolutivo" class="form-control font-monospace fw-bold" placeholder="Ej: No.: D.J. RES-042/17" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha del Dictamen Final *</label>
                            <input type="date" name="fecha_resolucion" class="form-control rounded-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Multa Económica (Pesos MXN) *</label>
                            <input type="number" step="0.01" name="monto_sancion_pesos" class="form-control rounded-1 fw-bold text-danger" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Sanciones Adicionales / Correctivas</label>
                        <input type="text" name="sancion_adicional" class="form-control rounded-1" placeholder="Ej: Clausura temporal de descarga y adecuación forzosa de trampa de grasa">
                    </div>

                    <div>
                        <label class="form-label fw-bold small text-uppercase text-muted">Considerandos y Fallo Definitivo *</label>
                        <textarea name="considerandos_legales" rows="4" class="form-control rounded-1" placeholder="Asiente la conclusión del dictamen jurídico..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-1 px-4 text-white fw-bold shadow-sm">
                        <i class="fa-solid fa-gavel me-1"></i> Imponer Sanción y Cerrar Caso
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
            Swal.fire({ title: '¡Emitido!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#dc3545' });
        @endif
    });
</script>
@endsection
