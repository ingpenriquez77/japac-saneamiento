<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resolutivo Administrativo JAPAC</title>
    <style>
        @page {
            margin: 2.5cm 2cm 2.5cm 2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333333;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #dc3545; /* Rojo de cierre definitivo */
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }

        /* 🖼️ Estilos para el Logotipo */
        .logo-box {
            width: 150px;
            vertical-align: middle;
            text-align: left;
        }

        .title-box {
            text-align: center;
            vertical-align: middle;
        }

        .logo-title h2 {
            margin: 0;
            color: #dc3545;
            font-size: 13.5pt;
            font-weight: bold;
        }
        .logo-title h3 {
            margin: 5px 0 0 0;
            color: #555555;
            font-size: 10.5pt;
            font-weight: normal;
        }
        .num-oficio {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 25px;
        }
        .content-justified {
            text-align: justify;
            margin-bottom: 15px;
        }

        /* 📊 Contenedor Penalizador */
        .section-analisis {
            width: 100%;
            margin: 20px 0;
        }
        .table-valores {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .table-valores th {
            background-color: #f2f2f2;
            border: 1px solid #dddddd;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }
        .table-valores td {
            border: 1px solid #dddddd;
            padding: 6px;
        }
        .bg-accent {
            background-color: #fff3cd;
            font-weight: bold;
        }

        /* 🖼️ Estilo Mazo Lateral */
        .gavel-cell {
            width: 180px;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #dddddd;
            background-color: #ffffff;
            padding: 4px;
        }

        .firmas-section {
            margin-top: 60px;
            width: 100%;
            text-align: center;
        }
        .firma-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
        }
        .linea-firma {
            margin-top: 50px;
            border-top: 1px solid #333333;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="logo-box">
                    @php
                        $pathLogo = 'dist/img/images.jpg';
                        $base64Logo = '';
                        if (file_exists(public_path($pathLogo))) {
                            $typeLogo = pathinfo(public_path($pathLogo), PATHINFO_EXTENSION);
                            $dataLogo = file_get_contents(public_path($pathLogo));
                            $base64Logo = 'data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
                        }
                    @endphp
                    @if($base64Logo)
                        <img src="{{ $base64Logo }}" style="max-height: 40px; max-width: 140px; object-fit: contain;">
                    @else
                        <span style="font-size: 12px; font-weight: bold; color: #dc3545;">[ LOGO JAPAC ]</span>
                    @endif
                </td>
                <td class="title-box">
                    <div class="logo-title">
                        <h2>JUNTA MUNICIPAL DE AGUA POTABLE Y ALCANTARILLADO DE CULIACÁN</h2>
                        <h3>SUBGERENCIA DE SANEAMIENTO Y CONTROL DE CALIDAD</h3>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="num-oficio">
        <strong>Resolutivo No.:</strong> <span style="font-family: monospace; font-weight: bold; color: red;">{{ $res->num_resolutivo }}</span><br>
        <strong>Juicio de Origen:</strong> {{ $res->inicioProcedimiento->num_oficio_inicio }}<br>
        <strong>Fecha de Emisión:</strong> {{ \Carbon\Carbon::parse($res->fecha_resolucion)->format('d/m/Y') }}
    </div>

    <p><strong>AL ESTABLECIMIENTO:</strong> {{ strtoupper($res->inicioProcedimiento->visita->establecimiento->nombre_establecimiento) }}<br>
    <strong>DIRECCIÓN:</strong> {{ strtoupper($res->inicioProcedimiento->visita->establecimiento->calle) }} #{{ $res->inicioProcedimiento->visita->establecimiento->num_exterior }}, COL. {{ strtoupper($res->inicioProcedimiento->visita->establecimiento->colonia) }}</p>

    <div class="content-justified">
        Vistos para resolver en definitiva los autos del expediente del procedimiento administrativo contencioso citados al rubro, se emite formalmente el <strong>RESOLUTIVO ADMINISTRATIVO DE SANCIÓN</strong> con base a los reglamentos oficiales de descargas vigentes de esta subgerencia.
    </div>

    <div class="section-analisis">
        <table class="table-valores">
            <thead>
                <tr>
                    <th>CONCEPTO / DICTAMEN PENALIZADOR</th>
                    <th>CUANTÍA / ACCIÓN MANDATORIA</th>
                    <th style="text-align: center; background-color: #e2e2e2;">CONTROL LEGAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">Sanción Económica Líquida</td>
                    <td style="color: red; font-weight: bold; font-size: 11pt;">${{ number_format($res->monto_sancion_pesos, 2) }} M.N.</td>
                    <td rowspan="3" class="gavel-cell">
                        @if($base64Logo)
                            <img src="{{ $base64Logo }}" style="width: 100%; max-height: 100px; object-fit: cover; display: block; margin: 0 auto;">
                        @else
                            <span style="font-size: 10px; color: #dc3545; font-weight: bold;">CASO CERRADO</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Medidas e Infracciones Adicionales</td>
                    <td>{{ $res->sancion_adicional ?? 'Ninguna dictaminada' }}</td>
                </tr>
                <tr class="bg-accent">
                    <td>ESTATUS FINAL DEL EXPEDIENTE</td>
                    <td style="color: #0d47a1; text-transform: uppercase;">{{ $res->status_final }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="content-justified">
        <strong>Considerandos Legales y Fallo Definitivo:</strong><br>
        {{ $res->considerandos_legales }}
    </div>

    <div class="content-justified" style="font-size: 10pt; font-style: italic;">
        Se le apercibe al establecimiento regulado que cuenta con los plazos de ley para realizar la liquidación total del adeudo ante las cajas fiscales de esta Junta Municipal para evitar la suspensión definitiva de los servicios de agua potable y drenaje sanitario.
    </div>

    <div class="firmas-section">
        <div class="firma-box">
            <div class="linea-firma"></div>
            <p><strong>Por JAPAC Saneamiento</strong><br>Área Jurídica Contenciosa</p>
        </div>
        <div class="firma-box">
            <div class="linea-firma"></div>
            <p><strong>Notificado en Conducción</strong><br>Firma de Recibido del Comercio</p>
        </div>
    </div>

</body>
</html>
