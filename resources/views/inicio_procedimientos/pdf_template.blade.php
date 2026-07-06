<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Inicio de Procedimiento Administrativo</title>
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
            border-bottom: 2px solid #0056b3;
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
            color: #0056b3;
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

        /* 📊 Contenedor en Bloques para Tabla e Imagen Lateral */
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
            background-color: #ebf5ff;
            font-weight: bold;
        }

        /* 🖼️ Estilo Celda de la Imagen Ilustrativa del Mazo */
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
                        <span style="font-size: 12px; font-weight: bold; color: #0056b3;">[ LOGO JAPAC ]</span>
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
        <strong>Oficio No.:</strong> <span style="font-family: monospace;">{{ $proc->num_oficio_inicio }}</span><br>
        <strong>Asunto:</strong> Inicio de Procedimiento Administrativo de Imposición de Cuotas<br>
        <strong>Fecha de Emisión:</strong> {{ \Carbon\Carbon::parse($proc->fecha_notificacion)->format('d/m/Y') }}
    </div>

    <p><strong>AL ESTABLECIMIENTO:</strong> {{ strtoupper($proc->visita->establecimiento->nombre_establecimiento) }}<br>
    <strong>DIRECCIÓN:</strong> {{ strtoupper($proc->visita->establecimiento->calle) }} #{{ $proc->visita->establecimiento->num_exterior }}, COL. {{ strtoupper($proc->visita->establecimiento->colonia) }}</p>

    <div class="content-justified">
        Por medio del presente y con base en las facultades otorgadas a esta Subgerencia de Saneamiento, se le notifica formalmente el <strong>Inicio del Procedimiento Administrativo</strong>. Este acto se deriva de los resultados analíticos físico-químicos obtenidos en la orden de inspección formal previa bajo el número de oficio <strong>{{ $proc->visita->num_oficioVI }}</strong>.
    </div>

    <div class="content-justified">
        Habiéndose evaluado las descargas en el sistema de alcantarillado sanitario municipal provenientes de los procesos de su empresa, se detectó un desapego a la normativa aplicable, registrando concentraciones de contaminantes básicos excedentes de acuerdo al marco legal regulatorio.
    </div>

    <div class="section-analisis">
        <table class="table-valores">
            <thead>
                <tr>
                    <th>CONTAMINANTE / PARÁMETRO EVALUADO</th>
                    <th>VALOR REQUERIDO</th>
                    <th>UNIDAD</th>
                    <th style="text-align: center; background-color: #e2e2e2;">ESTADO DEL PROCESO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Demanda Bioquímica de Oxígeno (DBO₅)</td>
                    <td>Límite Máximo: 150.00</td>
                    <td>Mg / Lt</td>
                    <td rowspan="4" class="gavel-cell">
                        @php
                            $pathGavel = 'dist/img/images.jpg';
                            $base64Gavel = '';
                            if (file_exists(public_path($pathGavel))) {
                                $typeGavel = pathinfo(public_path($pathGavel), PATHINFO_EXTENSION);
                                $dataGavel = file_get_contents(public_path($pathGavel));
                                $base64Gavel = 'data:image/' . $typeGavel . ';base64,' . base64_encode($dataGavel);
                            }
                        @endphp
                        @if($base64Gavel)
                            <img src="{{ $base64Gavel }}" style="width: 100%; max-height: 110px; object-fit: cover; display: block; margin: 0 auto;">
                        @else
                            <span style="font-size: 10px; color: #a1a1a1; font-weight: bold;">PROCEDIMIENTO RADICADO</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>SÓLIDOS SUSPENDIDOS TOTALES (SST)</td>
                    <td>Límite Máximo: 150.00</td>
                    <td>Mg / Lt</td>
                </tr>
                <tr>
                    <td>Grasas y Aceites (GyA)</td>
                    <td>Límite Máximo: 50.00</td>
                    <td>Mg / Lt</td>
                </tr>
                <tr class="bg-accent">
                    <td>TÉRMINO OTORGADO</td>
                    <td>{{ $proc->plazo_concedido }}</td>
                    <td>DÍAS</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="content-justified">
        <strong>Fundamento Legal Aplicado:</strong> {{ $proc->fundamento_legal }}. Se le concede el término dictaminado a partir de la fecha de notificación para presentarse ante las oficinas centrales de esta Junta de Agua Potable para manifestar lo que a su derecho convenga y solventar las anomalías detectadas.
    </div>

    <div class="content-justified">
        <strong>Hechos y Agravios que lo Motivan:</strong><br>
        {{ $proc->hechos_motivo }}
    </div>

    <div class="firmas-section">
        <div class="firma-box">
            <div class="linea-firma"></div>
            <p><strong>Por JAPAC Saneamiento</strong><br>Inspector / Dictaminador Asignado</p>
        </div>
        <div class="firma-box">
            <div class="linea-firma"></div>
            <p><strong>Recibió Notificación</strong><br>Representante Legal / Propietario</p>
        </div>
    </div>

</body>
</html>
