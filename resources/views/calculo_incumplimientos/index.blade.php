@extends('layouts.app')

@section('title', 'Índices de Incumplimiento')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-sm-6">
        <h2 class="mb-0 fw-bold text-dark" style="font-size: 26px;">Índice de Incumplimiento</h2>
    </div>
    <div class="col-sm-6 text-end">
        <span class="badge rounded-1 px-3 py-2 text-uppercase fw-bold shadow-sm" style="background-color: #1c2d42; color: #ffffff; font-size: 11px;">
            <i class="fa-solid fa-calculator me-1"></i> Régimen Tarifario / Cuotas por Exceso
        </span>
    </div>
</div>

<div class="mb-3 text-end">
    <button type="button" class="btn text-white shadow-sm fw-bold px-4 rounded-1 text-uppercase" style="background-color: #28a745; font-size: 13px;" data-bs-toggle="modal" data-bs-target="#calculoModal">
        <i class="fa-solid fa-plus me-2"></i> Nuevo Cálculo de Cuota
    </button>
</div>

<div class="card shadow-sm rounded-1 border-0" style="border-top: 3px solid #ffc107 !important;">
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title text-dark fw-bold mb-0" style="font-size: 15px;">
            <i class="fa-solid fa-file-invoice-dollar me-2 text-warning"></i> Historial de Tasaciones Emitidas
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead class="bg-light text-secondary fw-bold">
                    <tr>
                        <th class="ps-3 py-3">Establecimiento / Cuenta</th>
                        <th class="py-3">Muestreo</th>
                        <th class="py-3">Gasto (LPS) / Vol m³</th>
                        <th class="py-3">Análisis (DBO/SST/GyA)</th>
                        <th class="py-3">Índice Máx</th>
                        <th class="py-3">Carga Excedente</th>
                        <th class="text-end py-3">Cuota Mensual</th>
                        <th class="text-center py-3" style="width: 20%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calculos as $c)
                    <tr class="border-bottom">
                        <td class="ps-3">
                            <span class="fw-bold d-block text-dark">{{ $c->establecimiento->nombre_establecimiento }}</span>
                            <small class="text-muted font-monospace">Cuenta: {{ $c->establecimiento->cuenta }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-secondary">{{ \Carbon\Carbon::parse($c->fecha_muestreo)->format('d/m/Y') }}</span>
                            <small class="d-block text-xs text-uppercase text-muted">{{ $c->laboratorio_analisis }}</small>
                        </td>
                        <td>
                            <span class="d-block">Gasto: <strong>{{ number_format($c->gasto_medio_diario_lps, 3) }} LPS</strong></span>
                            <small class="text-muted">Vol: {{ number_format($c->volumen_mensual_m3, 2) }} m³</small>
                        </td>
                        <td>
                            <div class="text-xs">
                                <span class="d-block text-dark">DBO: <strong>{{ $c->resultado_dbo }}</strong> <span class="text-muted">/{{ intval($c->limite_dbo) }}</span></span>
                                <span class="d-block text-dark">SST: <strong>{{ $c->resultado_sst }}</strong> <span class="text-muted">/{{ intval($c->limite_sst) }}</span></span>
                                <span class="d-block text-dark">GyA: <strong>{{ $c->resultado_gya }}</strong> <span class="text-muted">/{{ intval($c->limite_gya) }}</span></span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-danger text-uppercase px-2 py-1 mb-1" style="font-size: 10px;">{{ $c->contaminante_predominante }}</span>
                            <span class="d-block fw-bold text-dark font-monospace">{{ number_format($c->indice_predominante_final, 2) }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ number_format($c->carga_contaminante_kg, 2) }} Kg</span>
                            <small class="d-block text-muted">Tarifa: ${{ number_format($c->cuota_por_kg, 3) }}</small>
                        </td>
                        <td class="text-end fw-bold text-success pe-4" style="font-size: 14.5px;">
                            ${{ number_format($c->monto_pagar_mes, 2) }} MXN
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1 justify-content-center w-100">
                                <a href="{{ route('calculo_incumplimientos.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm px-2 py-1 rounded-1 fw-bold">
                                    <i class="fa-solid fa-file-pdf me-1"></i> Formato JAPAC
                                </a>
                                <form action="{{ route('calculo_incumplimientos.destroy', $c->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este cálculo financiero?');" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-calculator fa-2x mb-2 d-block opacity-50"></i> No se han procesado cálculos de cuotas en este periodo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="calculoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-1 border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-calculator me-2 text-warning"></i> Aplicación de Régimen Tarifario JAPAC</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('calculo_incumplimientos.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-light p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Seleccionar Establecimiento *</label>
                            <select name="establecimiento_id" class="form-select rounded-1" required>
                                <option value="">-- Seleccione del padrón activo --</option>
                                @foreach($establecimientos as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre_establecimiento }} (Cuenta: {{ $e->cuenta }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Fecha del Muestreo *</label>
                            <input type="date" name="fecha_muestreo" class="form-control rounded-1" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Gasto Medio Diario (LPS) *</label>
                            <input type="number" name="gasto_medio_diario_lps" step="0.0001" class="form-control rounded-1" placeholder="Ej: 0.079" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Laboratorio Dictaminador *</label>
                            <select name="laboratorio_analisis" class="form-select rounded-1">
                                <option value="JAPAC">LABORATORIO INTERNO JAPAC</option>
                                <option value="LAB_EXTERNO">LABORATORIO EXTERNO ACREDITADO</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-1 shadow-sm border">
                        <h6 class="text-uppercase fw-bold mb-3 border-bottom pb-2 text-primary">
                            <i class="fa-solid fa-flask-vial me-2"></i> Concentraciones Detectadas (Resultados en Mg/Lt)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">DEMANDA BIOQUÍMICA DE OXÍGENO (DBO₅)</label>
                                <input type="number" name="resultado_dbo" step="0.01" class="form-control border-primary" placeholder="Límite Promedio: 150" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">SÓLIDOS SUSPENDIDOS TOTALES (SST)</label>
                                <input type="number" name="resultado_sst" step="0.01" class="form-control border-primary" placeholder="Límite Promedio: 150" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">GRASAS Y ACEITES (GyA)</label>
                                <input type="number" name="resultado_gya" step="0.01" class="form-control border-primary" placeholder="Límite Promedio: 50" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Observaciones Adicionales</label>
                        <textarea name="observaciones" rows="2" class="form-control rounded-1" placeholder="Detalles del cálculo financiero..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-1 px-4 text-white fw-bold shadow-sm">
                        <i class="fa-solid fa-calculator me-1"></i> Procesar Cuota Excedente
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
            Swal.fire({ title: '¡Tasado!', text: "{{ session('success') }}", icon: 'success', confirmButtonColor: '#28a745' });
        @endif
    });
</script>
@endsection
