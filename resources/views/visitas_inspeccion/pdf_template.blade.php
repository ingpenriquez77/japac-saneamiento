<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Visita de Inspección</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; color: #0056b3; text-uppercase: true; }
        .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .meta-table td { padding: 5px; }
        .section-title { font-size: 13px; font-weight: bold; background-color: #f2f2f2; padding: 5px; margin-top: 20px; color: #1c2d42; }
        .content-box { border: 1px solid #ccc; padding: 10px; min-height: 80px; margin-top: 5px; font-family: monospace; }
        .footer { position: fixed; bottom: 30px; left: 0; right: 0; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">JAPAC - AGUA Y SANEAMIENTO</div>
        <div>DIRECCIÓN DE CONTROL DE DESCARGAS Y SANEAMIENTO</div>
        <div style="font-weight: bold; margin-top: 5px;">ORDEN OFICIAL DE VISITA DE INSPECCIÓN</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; font-weight: bold;">No. Visita Inspección:</td>
            <td style="width: 25%; font-family: monospace;">{{ $visita->num_visita_inspeccion }}</td>
            <td style="width: 25%; font-weight: bold;">No. Oficio Dictamen:</td>
            <td style="width: 25%; font-family: monospace; color: #0056b3; font-weight: bold;">{{ $visita->num_oficioVI }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Fecha y Hora:</td>
            <td>{{ \Carbon\Carbon::parse($visita->fechavisita_inspeccion)->format('d/m/Y H:i') }} Hrs.</td>
            <td style="font-weight: bold;">Estatus Inicial:</td>
            <td><span style="text-uppercase: true; font-weight: bold;">{{ $visita->status }}</span></td>
        </tr>
    </table>

    <div class="section-title">DATOS DEL ESTABLECIMIENTO REGULADO</div>
    <table class="meta-table" style="margin-top: 5px;">
        <tr>
            <td style="width: 20%; font-weight: bold;">Nombre Comercial:</td>
            <td style="font-size: 13px; font-weight: bold;">{{ $visita->establecimiento->nombre_establecimiento }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Cuenta JAPAC:</td>
            <td style="font-family: monospace;">{{ $visita->establecimiento->cuenta ?? 'N/E' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Giro / Actividad:</td>
            <td>{{ $visita->establecimiento->actividad ?? 'No especificada' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Ubicación:</td>
            <td>
                {{ $visita->establecimiento->calle }} #{{ $visita->establecimiento->num_exterior }},
                Col. {{ $visita->establecimiento->colonia }}, C.P. {{ $visita->establecimiento->codigo_postal }}, Culiacán, Sinaloa.
            </td>
        </tr>
    </table>

    <div class="section-title">HECHOS CONSTATADOS Y OBSERVACIONES DE CAMPO</div>
    <div class="content-box">
        {{ $visita->observaciones ?? 'Sin observaciones asentadas al momento del levantamiento de la orden.' }}
    </div>

    <div style="margin-top: 60px; text-align: center;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <br><br>____________________________________<br>
                    <strong>Inspector Técnico Asignado</strong><br>
                    Área de Saneamiento JAPAC
                </td>
                <td style="width: 50%; text-align: center;">
                    <br><br>____________________________________<br>
                    <strong>Sello y Firma de Recibido</strong><br>
                    Representante Legal / Encargado
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Junta Municipal de Agua Potable y Alcantarillado de Culiacán<br>
        Documento Oficial Informativo del Sistema de Control de Descargas Saneamiento JAPAC v4.0
    </div>

</body>
</html>
