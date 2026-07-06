<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Infracción #{{ $inf->num_folio }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 10px; background-color: #fff5f6; }
        .boleta-container { border: 2px solid #f76c83; padding: 20px; background-color: #ffffff; border-radius: 5px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-title { font-size: 16px; font-weight: bold; color: #b3394b; text-align: center; }
        .folio-box { border: 2px solid #ff0000; color: #ff0000; font-size: 18px; font-weight: bold; padding: 8px 15px; text-align: center; font-family: monospace; }
        .section-banner { background-color: #f76c83; color: white; font-weight: bold; padding: 5px 10px; text-transform: uppercase; font-size: 11px; margin-top: 15px; margin-bottom: 10px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table td { padding: 6px; border-bottom: 1px solid #f2f2f2; vertical-align: top; }
        .label { font-weight: bold; color: #555; text-transform: uppercase; font-size: 10px; }
        .check-list-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .check-list-table td { padding: 8px; border: 1px solid #f76c83; font-size: 11px; }
        .box-checked { background-color: #f76c83; color: white; font-weight: bold; text-align: center; width: 25px; }
        .box-empty { text-align: center; width: 25px; color: #ccc; }
        .observaciones-box { border: 1px solid #ccc; padding: 12px; background-color: #fafafa; font-style: italic; min-height: 80px; line-height: 1.5; margin-top: 5px; }
        .footer-text { margin-top: 25px; font-size: 9px; color: #666; text-align: justify; line-height: 1.4; }
    </style>
</head>
<body>

<div class="boleta-container">
    <table class="header-table">
        <tr>
            <td style="width: 75%;">
                <div style="font-weight: bold; font-size: 13px; color: #1c2d42;">JUNTA MUNICIPAL DE AGUA POTABLE Y ALCANTARILLADO DE CULIACÁN</div>
                <div style="font-size: 10px; color: #555; margin-top: 3px;">ÁREA DE SANEAMIENTO / CONTROL DE DESCARGAS</div>
                <div class="header-title" style="text-align: left; margin-top: 10px;">ACTA DE INFRACCIÓN / RECORRIDO RUTINARIO</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div class="folio-box"># {{ $inf->num_folio }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table" style="background-color: #fff1f3;">
        <tr>
            <td style="width: 33%;"><span class="label">Día:</span> <strong>{{ \Carbon\Carbon::parse($inf->fecha_infraccion)->format('d') }}</strong></td>
            <td style="width: 33%;"><span class="label">Mes:</span> <strong>{{ \Carbon\Carbon::parse($inf->fecha_infraccion)->format('m') }}</strong></td>
            <td style="width: 34%;"><span class="label">Año:</span> <strong>{{ \Carbon\Carbon::parse($inf->fecha_infraccion)->format('Y') }}</strong></td>
        </tr>
        <tr>
            <td colspan="3"><span class="label">Hora del Suceso:</span> SIENDO LAS <strong>{{ $inf->hora_infraccion }} HRS</strong> HACIENDO RECORRIDO RUTINARIO EL C. INSPECTOR DE SANEAMIENTO.</td>
        </tr>
    </table>

    <div class="section-banner">Localización del Infractor / Establecimiento</div>
    <table class="data-table">
        <tr>
            <td colspan="2"><span class="label">Nombre Comercial:</span> <span style="font-size: 13px; font-weight: bold;">{{ strtoupper($inf->nombre_establecimiento_informal) }}</span></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Domicilio:</span> {{ strtoupper($inf->domicilio_informal) }}</td>
        </tr>
        <tr>
            <td style="width: 50%;"><span class="label">No. Medidor:</span> <strong>{{ $inf->num_medidor_informal ?? 'N/E' }}</strong></td>
            <td style="width: 50%;"><span class="label">Cuenta Contrato JAPAC:</span> <strong>{{ $inf->cuenta_informal ?? 'N/E' }}</strong></td>
        </tr>
        <tr>
            <td colspan="2"><span class="label">Señas Particulares del Predio:</span> {{ strtoupper($inf->señas_particulares ?? 'NINGUNA') }}</td>
        </tr>
    </table>

    <div class="section-banner">Irregularidades Detectadas (Fundamento en Ley de Agua del Estado de Sinaloa)</div>
    <table class="check-list-table">
        <tr>
            <td class="{{ $inf->anomalia_grasas_aceites ? 'box-checked' : 'box-empty' }}">{{ $inf->anomalia_grasas_aceites ? 'X' : '' }}</td>
            <td><strong>DESCARGAR GRASAS Y ACEITES AL DRENAJE SANITARIO</strong></td>
        </tr>
        <tr>
            <td class="{{ $inf->anomalia_sin_permiso ? 'box-checked' : 'box-empty' }}">{{ $inf->anomalia_sin_permiso ? 'X' : '' }}</td>
            <td>DESCARGAR AGUAS RESIDUALES SIN PERMISO</td>
        </tr>
        <tr>
            <td class="{{ $inf->anomalia_residuos_toxicos ? 'box-checked' : 'box-empty' }}">{{ $inf->anomalia_residuos_toxicos ? 'X' : '' }}</td>
            <td>DESCARGAR RESIDUOS TÓXICOS O PROHIBIDOS AL DRENAJE SANITARIO</td>
        </tr>
        <tr>
            <td class="{{ $inf->anomalia_aguas_pluviales ? 'box-checked' : 'box-empty' }}">{{ $inf->anomalia_aguas_pluviales ? 'X' : '' }}</td>
            <td>DESCARGAR AGUAS PLUVIALES AL DRENAJE SANITARIO</td>
        </tr>
        <tr>
            <td class="{{ $inf->anomalia_sin_registro_banqueta ? 'box-checked' : 'box-empty' }}">{{ $inf->anomalia_sin_registro_banqueta ? 'X' : '' }}</td>
            <td>NO CUENTA CON REGISTRO DE BANQUETA</td>
        </tr>
    </table>

    <div class="section-banner">Observaciones y Dictamen de Campo</div>
    <div class="observaciones-box">
        {{ $inf->observaciones_campo ?? 'Sin observaciones anotadas al momento de la inspección.' }}
    </div>

    <table class="data-table" style="margin-top: 15px;">
        <tr>
            <td><span class="label">Notificación efectuada:</span> <strong>{{ strtoupper($inf->recibio_notificacion) }}</strong></td>
            <td><span class="label">Inspector Técnico:</span> <strong>{{ strtoupper($inf->inspector->nombre ?? 'ADMIN') }} {{ strtoupper($inf->inspector->paterno ?? 'JAPAC') }}</strong></td>
        </tr>
    </table>

    <div class="footer-text">
        INFRINGIENDO ASÍ A LOS ARTÍCULOS 16, 23, 24, 30, 37, 80 Y 91 DE LA LEY DE AGUA POTABLE Y ALCANTARILLADO DEL ESTADO DE SINALOA. CUENTA USTED CON (5) DÍAS HÁBILES CONTANDO A PARTIR DE LA FECHA DE SU NOTIFICACIÓN PARA PASAR AL DOMICILIO DE NUESTRAS OFICINAS ARRIBA SEÑALADAS A CUBRIR ESTA INFRACCIÓN, DE LO CONTRARIO SE APLICARÁ EL PROCEDIMIENTO ECONÓMICO COACTIVO.
    </div>
</div>

</body>
</html>
