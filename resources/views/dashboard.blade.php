@extends('layouts.app')

@section('title', 'Dashboard')

@section('content_header', 'Panel de Control - Saneamiento')

@section('content')
<div class="row mb-4">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-info shadow-sm p-3 rounded-0 border-0 text-white">
            <div class="inner">
                <h3>{{ \App\Models\Establecimiento::count() }}</h3>
                <p>Establecimientos Registrados</p>
            </div>
            <div class="icon position-absolute end-0 top-0 mt-2 me-3 opacity-25">
                <i class="fa-solid fa-building fa-3x"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-success shadow-sm p-3 rounded-0 border-0 text-white">
            <div class="inner">
                <h3>{{ \App\Models\VisitaInspeccion::count() }}</h3>
                <p>Visitas de Inspección Formles</p>
            </div>
            <div class="icon position-absolute end-0 top-0 mt-2 me-3 opacity-25">
                <i class="fa-solid fa-clipboard-check fa-3x"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-warning shadow-sm p-3 rounded-0 border-0 text-dark">
            <div class="inner fw-bold">
                <h3>{{ \App\Models\InicioProcedimiento::count() }}</h3>
                <p>Inicios de Procedimiento</p>
            </div>
            <div class="icon position-absolute end-0 top-0 mt-2 me-3 opacity-25">
                <i class="fa-solid fa-gavel fa-3x"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-danger shadow-sm p-3 rounded-0 border-0 text-white">
            <div class="inner">
                <h3>{{ \App\Models\ResolutivoAdministrativo::count() }}</h3>
                <p>Resolutivos Finales Emitidos</p>
            </div>
            <div class="icon position-absolute end-0 top-0 mt-2 me-3 opacity-25">
                <i class="fa-solid fa-scale-balanced fa-3x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm rounded-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title text-dark fw-bold mb-0" style="font-size: 16px;">
                    <i class="fa-solid fa-route text-primary me-2"></i> Monitor de Trazabilidad de Expedientes Formales (Línea de Proceso)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                        <thead class="bg-light text-secondary fw-bold" style="font-size: 12px;">
                            <tr>
                                <th class="ps-4 py-3">Establecimiento</th>
                                <th class="py-3 text-center" style="width: 22%;">Paso 1: Visita Inspección</th>
                                <th class="py-3 text-center" style="width: 22%;">Paso 2: Inicio Procedimiento</th>
                                <th class="py-3 text-center" style="width: 22%;">Paso 3: Resolutivo Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Cargamos las visitas formalizadas junto con sus herederos en la cadena legal
                                $lineasProceso = \App\Models\VisitaInspeccion::with(['establecimiento', 'inicioProcedimiento.resolutivo'])->orderBy('id', 'desc')->take(10)->get();
                            @endphp

                            @forelse($lineasProceso as $visita)
                            @php
                                $inicio = $visita->inicioProcedimiento; // Relación HasOne asumida
                                $resolutivo = $inicio ? $inicio->resolutivo : null; // Relación HasOne asumida
                            @endphp
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <span class="fw-bold d-block text-dark" style="font-size: 14px;">{{ $visita->establecimiento->nombre_establecimiento }}</span>
                                    <small class="text-muted font-monospace">Cuenta: {{ $visita->establecimiento->cuenta_comercial ?? 'N/E' }}</small>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success rounded-0 p-2 font-monospace d-block mb-1">
                                        <i class="fa-solid fa-file-signature me-1"></i> {{ $visita->num_oficioVI }}
                                    </span>
                                    <small class="text-muted">Emitido: {{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y') }}</small>
                                </td>

                                <td class="text-center bg-light bg-opacity-25">
                                    @if($inicio)
                                        <span class="badge bg-warning text-dark rounded-0 p-2 font-monospace d-block mb-1">
                                            <i class="fa-solid fa-gavel me-1"></i> {{ $inicio->num_oficio_inicio }}
                                        </span>
                                        <small class="text-muted">Notificado: {{ \Carbon\Carbon::parse($inicio->fecha_notificacion)->format('d/m/Y') }}</small>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-0 p-2 opacity-50 d-block mb-1">
                                            <i class="fa-solid fa-hourglass-start me-1"></i> Pendiente Radicar
                                        </span>
                                        <small class="text-muted-50 text-xs">-</small>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($resolutivo)
                                        <span class="badge bg-danger rounded-0 p-2 font-monospace d-block mb-1">
                                            <i class="fa-solid fa-scale-balanced me-1"></i> {{ $resolutivo->num_resolutivo }}
                                        </span>
                                        <small class="text-danger fw-bold">Sanción: ${{ number_format($resolutivo->monto_sancion_pesos, 2) }}</small>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-0 p-2 opacity-50 d-block mb-1">
                                            <i class="fa-solid fa-ban me-1"></i> Sin Resolución
                                        </span>
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i> Sin registros en la cola de trazabilidad formal.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4 rounded-0">
            <div class="card-body">
                <h5 class="card-title text-primary fw-bold mb-3">
                    <i class="fa-solid fa-droplet me-2"></i> Ecosistema Operativo de Saneamiento JAPAC
                </h5>
                <p class="card-text text-muted mb-0">
                    Bienvenido al centro de mando. Este panel consolida el flujo legal completo de descargas de aguas residuales comerciales e industriales de Culiacán. Puedes navegar mediante la barra lateral para registrar nuevas órdenes o aplicar las multas correspondientes según el Régimen Tarifario Vigente.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
