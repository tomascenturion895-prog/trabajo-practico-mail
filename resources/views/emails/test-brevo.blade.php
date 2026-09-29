<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>{{ $asunto }}</title>
</head>

<body style="
    margin:0;
    padding:0;
    background:#f3f4f6;
    font-family:Arial, Helvetica, sans-serif;
">

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    style="padding:30px 15px;">

    <tr>

        <td align="center">

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                style="
                    max-width:600px;
                    background:#ffffff;
                    border-radius:10px;
                    overflow:hidden;
                ">

                {{-- Encabezado --}}
                <tr>

                    <td style="
                        background:#1f2937;
                        padding:25px;
                        text-align:center;
                        color:white;
                    ">

                        <h2 style="margin:0;">
                            Notificación
                        </h2>

                    </td>

                </tr>


                {{-- Contenido --}}
                <tr>

                    <td style="
                        padding:30px;
                        color:#374151;
                        font-size:16px;
                        line-height:1.6;
                    ">

                        <p>
                            Hola <strong>{{ $nombre }}</strong>,
                        </p>

                        <p style="white-space:pre-line;">{{ $mensaje }}</p>

                    </td>

                </tr>


                {{-- Pie --}}
                <tr>

                    <td style="
                        padding:20px;
                        text-align:center;
                        background:#f9fafb;
                        color:#6b7280;
                        font-size:12px;
                    ">

                        © {{ date('Y') }}
                        Sistema de Notificaciones

                    </td>

                </tr>

            </table>

        </td>

    </tr>

</table>

</body>
</html>
