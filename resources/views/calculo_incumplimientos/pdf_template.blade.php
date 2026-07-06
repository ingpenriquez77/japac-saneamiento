<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Cálculo de Cuotas JAPAC</title>
    <style>
        @page { margin: 18px; }
        body { font-family: 'Arial', sans-serif; font-size: 10px; color: #000; line-height: 1.25; }
        .wrapper { border: 1.5px solid #000; padding: 4px; min-height: 98%; }

        /* Tablas */
        .w-100 { width: 100%; }
        table { border-collapse: collapse; margin-bottom: 5px; }
        table.table-border td, table.table-border th { border: 1px solid #000; padding: 4.5px 5px; }

        /* Cabecera oficial */
        .logo-box { width: 22%; border: 1px solid #000; text-align: center; padding: 2px; vertical-align: middle; background-color: #ffffff; }
        .title-box { text-align: center; font-weight: bold; font-size: 11px; border: 1px solid #000; padding: 5px; background-color: #bbdefb; }

        /* Componentes Calcados */
        .banner { background-color: #bbdefb; text-align: center; font-weight: bold; font-size: 11px; border: 1px solid #000; padding: 5px 0; margin: 5px 0; letter-spacing: 0.5px; }
        .legal-text { border: 1px solid #000; padding: 5px 8px; text-align: justify; font-size: 9.5px; margin-bottom: 6px; font-weight: 500; }

        /* 📦 NUEVO: Contenedor Oficial "MONTO A PAGAR" */
        .monto-pagar-box { border: 1px solid #000; padding: 6px 8px; text-align: justify; font-size: 9.5px; margin-bottom: 6px; font-weight: bold; }

        /* Paleta de Colores Ajustada de forma Precisa */
        .bg-label { background-color: #ffffff; font-weight: bold; font-size: 9.5px; }

        /* 🟩 1. Celdas Superiores y Unidades (Fondo Verde Clorofila) */
        .bg-unit-green { background-color: #c8e6c9; text-align: center; font-weight: bold; font-size: 9.5px; }

        /* 🟦 2. Celdas de Datos Numéricos Centrales e Índices (Fondo Azul Claro) */
        .bg-value-blue { background-color: #eaf4fa; text-align: center; font-weight: bold; font-family: monospace; font-size: 11px; }

        .bg-value-white { background-color: #ffffff; text-align: center; font-weight: bold; font-family: monospace; font-size: 11px; }
        .bg-active-row { background-color: #bbdefb !important; font-weight: bold; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .footer-note { font-size: 8.5px; text-align: center; margin-top: 4px; font-style: italic; }
    </style>
</head>
<body>

<div class="wrapper">

    <table class="w-100">
        <tr>
            <td class="logo-box">
                @php
                    $path = 'dist/img/images.jpg';
                    $base64 = '';
                    if (file_exists(public_path($path))) {
                        $type = pathinfo(public_path($path), PATHINFO_EXTENSION);
                        $data = file_get_contents(public_path($path));
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" style="max-height: 38px; max-width: 130px; object-fit: contain;">
                @else
                    <span style="font-size: 13px; font-weight: bold; color: #0056b3;">JAPAC</span>
                @endif
            </td>
            <td class="title-box">
                <div style="font-size: 12px; letter-spacing: 0.5px; color: #0d47a1;">JUNTA MUNICIPAL DE AGUA POTABLE Y ALCANTARILLADO DE CULIACÁN</div>
                <div style="font-size: 9.5px; margin-top: 3px; font-weight: bold; color: #1565c0;">SUBGERENCIA DE SANEAMIENTO Y CONTROL DE CALIDAD</div>
            </td>
        </tr>
    </table>

    <table class="w-100 table-border">
        <tr>
            <td class="bg-label" style="width: 12%;">NOMBRE</td>
            <td style="width: 48%; font-weight: bold; font-size: 11px;">{{ strtoupper($calculo->establecimiento->nombre_establecimiento) }}</td>
            <td class="bg-label" style="width: 20%;">FECHA MUESTREO</td>
            <td style="width: 20%;" class="text-center fw-bold bg-value-white">{{ \Carbon\Carbon::parse($calculo->fecha_muestreo)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bg-label">DIRECCIÓN</td>
            <td style="font-size: 9.5px;">{{ strtoupper($calculo->establecimiento->calle) }} #{{ $calculo->establecimiento->num_exterior }}, COL. {{ strtoupper($calculo->establecimiento->colonia) }}</td>
            <td class="bg-label">LABORATORIO</td>
            <td class="text-center fw-bold bg-value-white">{{ strtoupper($calculo->laboratorio_analisis) }}</td>
        </tr>
    </table>

    <div class="banner" style="color: #0d47a1;">CALCULO DE LA APLICACIÓN DE CUOTAS INDICADAS EN EL REGIMEN TARIFARIO VIGENTE</div>

    <div class="legal-text">
        CON BASE A LOS ANALISIS FISICOQUIMICOS REFERIDOS, QUE SE VIERTEN IMPLICITAMENTE POR LA DESCARGA DE AGUAS RESIDUALES AL SISTEMA DE ALCANTARILLADO SANITARIO MUNICIPAL PROVENIENTE DE LOS PROCESOS DE LA EMPRESA.
    </div>

    <div class="monto-pagar-box">
        MONTO A PAGAR:<br>
        <span style="font-weight: normal; font-size: 9px;">CONSIDERANDO LA TABLA III, PARA CONTAMINANTES BASICOS (DEMANDA BIOQUIMICA DE OXIGENO, SOLIDOS SUSPENDIDOS TOTALES Y GRASAS Y ACEITES), LA CUOTA POR PESOS SOBRE KILOGRAMO, EXCEDENTE DESCARGADO PARA CONTAMINANTES BASICOS, ESTABLECIDA EN EL PUNTO 8.2 DEL REGIMEN TARIFARIO VIGENTE DE LA JUNTA MUNICIPAL DE AGUA POTABLE Y ALCANTARILLADO DE CULIACAN, PUBLICADO EN EL DIARIO OFICIAL DEL ESTADO DE SINALOA, EL 06 DE AGOSTO DE 2003, SE TIENE LO SIGUIENTE:</span>
    </div>

    <div style="font-weight: bold; font-size: 9.5px; margin: 3px 0 2px 2px;">RESULTADO DE ANALISIS FISICOQUIMICOS PARA CONTAMINANTES BASICOS PRESENTADOS</div>
    <table class="w-100 table-border">
        <tr>
            <td style="width: 60%; font-weight: bold;">DEMANDA BIOQUIMICA DE OXIGENO (DBO₅)</td>
            <td style="width: 20%;" class="bg-value-blue">{{ number_format($calculo->resultado_dbo, 2) }}</td>
            <td style="width: 20%;" class="bg-unit-green">Mg / Lt</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">SOLIDOS SUSPENDIDOS TOTALES (SST)</td>
            <td class="bg-value-blue">{{ number_format($calculo->resultado_sst, 2) }}</td>
            <td class="bg-unit-green">Mg / Lt</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">GRASAS Y ACEITES (GyA)</td>
            <td class="bg-value-blue">{{ number_format($calculo->resultado_gya, 2) }}</td>
            <td class="bg-unit-green">Mg / Lt</td>
        </tr>
    </table>

    <table class="w-100 table-border" style="margin-top: 6px;">
        <tr>
            <td style="width: 60%;" class="bg-label">GASTO MEDIO DIARIO</td>
            <td style="width: 20%;" class="bg-value-blue">{{ number_format($calculo->gasto_medio_diario_lps, 3) }}</td>
            <td style="width: 20%;" class="bg-unit-green">LPS</td>
        </tr>
        <tr>
            <td class="bg-label">VOLUMEN POR MES</td>
            <td class="bg-value-blue">{{ number_format($calculo->volumen_mensual_m3, 2) }}</td>
            <td class="bg-unit-green">M³</td>
        </tr>
        <tr>
            <td class="bg-label">VALOR DEL CONTAMINANTE BASICO INCUMPLIDO</td>
            <td class="bg-value-blue" style="color: red; font-size: 11.5px; font-family: monospace;">
                @if($calculo->contaminante_predominante === 'DBO') {{ number_format($calculo->resultado_dbo, 2) }}
                @elseif($calculo->contaminante_predominante === 'SST') {{ number_format($calculo->resultado_sst, 2) }}
                @else {{ number_format($calculo->resultado_gya, 2) }} @endif
            </td>
            <td class="bg-unit-green">Mg / Lt</td>
        </tr>
        <tr>
            <td class="bg-label">INDICE DE INCUMPLIMIENTO</td>
            <td colspan="2" class="bg-value-blue" style="color: #0d47a1; font-size: 12px; letter-spacing: 1px;">
                {{ number_format($calculo->indice_predominante_final, 2) }}
            </td>
        </tr>
    </table>

    <div class="legal-text text-center" style="font-weight: bold; background-color: #ffffff; padding: 7px; margin-top: 4px;">
        SEGÚN TABLA III EL INDICE DE INCUMPLIMIENTO PARA ESTE VALOR CORRESPONDE UN PAGO DEL CONTAMINANTE DE:
        <div style="font-size: 14px; margin-top: 3px; color: #0d47a1; font-family: monospace;">${{ number_format($calculo->cuota_por_kg, 3) }}</div>
    </div>

    <table class="w-100 table-border" style="margin-top: 4px;">
        <tr>
            <td style="width: 60%;" class="bg-label">CARGA CONTAMINANTE</td>
            <td style="width: 20%;" class="bg-value-blue">{{ number_format($calculo->carga_contaminante_kg, 2) }}</td>
            <td style="width: 20%;" class="text-center bg-label bg-unit-green">Kg</td>
        </tr>
        <tr>
            <td class="bg-label">MONTO A PAGAR POR MES ($)</td>
            <td class="text-center fw-bold bg-value-blue" style="padding-right: 10px; font-size: 12px; font-family: monospace;">$ {{ number_format($calculo->monto_pagar_mes, 2) }}</td>
            <td class="text-center bg-label bg-unit-green">M.N.</td>
        </tr>
        <tr>
            <td class="bg-label">MONTO A PAGAR POR DOCE MESES ($)</td>
            <td class="text-center fw-bold bg-value-blue style="padding-right: 10px; font-size: 12px; font-family: monospace;">$ {{ number_format($calculo->monto_pagar_anual, 2) }}</td>
            <td class="text-center bg-label bg-unit-green">M.N.</td>
        </tr>
    </table>

    <div style="font-weight: bold; font-size: 9px; margin: 5px 0 2px 2px; text-transform: uppercase;">Limite Maximo Permisible Para Contaminantes Basicos De Acuerdo A La Tabla I Promedio Mensual</div>
    <table class="w-100 table-border text-center" style="font-size: 9px;">
        <thead class="bg-label">
            <tr>
                <th style="text-align: left; width: 60%;">PARAMETRO</th>
                <th style="width: 20%;">DIARIO</th>
                <th style="width: 20%;">MENSUAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: left; font-weight: bold;">DEMANDA BIOQUIMICA DE OXIGENO (DBO₅)</td>
                <td>200</td>
                <td class="fw-bold" style="background-color: #ffcc80;">150</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold;">SOLIDOS SUSPENDIDOS TOTALES (SST)</td>
                <td>200</td>
                <td class="fw-bold" style="background-color: #ffcc80;">150</td>
            </tr>
            <tr>
                <td style="text-align: left; font-weight: bold;">GRASAS Y ACEITES</td>
                <td>75</td>
                <td class="fw-bold" style="background-color: #ffcc80;">50</td>
            </tr>
        </tbody>
    </table>

    <div style="font-weight: bold; font-size: 9px; margin: 5px 0 2px 2px; text-transform: uppercase;">Tabla III: Cuota en Pesos Sobre Kg Excedente Descargado</div>
    <table class="w-100 table-border text-center" style="font-size: 8.5px;">
        <thead class="bg-label">
            <tr>
                <th style="width: 60%;">INDICE DE INCUMPLIMIENTO</th>
                <th style="width: 40%;">PESOS / Kg</th>
            </tr>
        </thead>
        <tbody>
            <tr class="{{ $calculo->indice_predominante_final <= 0.10 ? 'bg-active-row' : '' }}">
                <td>DE 0.00 HASTA 0.10</td>
                <td>EXENTO</td>
            </tr>
            <tr class="{{ ($calculo->indice_predominante_final > 0.10 && $calculo->indice_predominante_final <= 1.00) ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 0.10 HASTA 1.00</td>
                <td>5.808</td>
            </tr>
            <tr class="{{ ($calculo->indice_predominante_final > 1.00 && $calculo->indice_predominante_final <= 2.00) ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 1.00 HASTA 2.00</td>
                <td>7.373</td>
            </tr>
            <tr class="{{ ($calculo->indice_predominante_final > 2.00 && $calculo->indice_predominante_final <= 3.00) ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 2.00 HASTA 3.00</td>
                <td>9.043</td>
            </tr>
            <tr class="{{ ($calculo->indice_predominante_final > 3.00 && $calculo->indice_predominante_final <= 4.00) ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 3.00 HASTA 4.00</td>
                <td>9.885</td>
            </tr>
            <tr class="{{ ($calculo->indice_predominante_final > 4.00 && $calculo->indice_predominante_final <= 5.00) ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 4.00 HASTA 5.00</td>
                <td>10.315</td>
            </tr>
            <tr class="{{ $calculo->indice_predominante_final > 5.00 ? 'bg-active-row' : '' }}">
                <td>MAYOR DE 5.00</td>
                <td>10.837</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        Documento oficial emitido por el Departamento de Saneamiento JAPAC. Copia certificada del historial electrónico de tasaciones.
    </div>

</div>

</body>
</html>
