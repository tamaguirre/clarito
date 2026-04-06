<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitacion registro empresa</title>
</head>
<body style="margin:0;padding:24px;background:#f3f7fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:620px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;">
        <h1 style="margin:0 0 12px;font-size:20px;color:#0f1f3d;">Bienvenido a Clarito</h1>
        <p style="margin:0 0 12px;line-height:1.6;">Hola,</p>
        <p style="margin:0 0 16px;line-height:1.6;">
            Hemos registrado a <strong>{{ $companyName }}</strong> y necesitamos que completes el proceso de onboarding.
        </p>
        <p style="margin:0 0 20px;line-height:1.6;">
            Presiona el siguiente boton para definir tipo de empresa, diccionario interno, password, logo y descripcion.
        </p>
        <p style="margin:0 0 24px;">
            <a href="{{ $completionUrl }}" style="display:inline-block;background:#00bcd4;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:10px;font-weight:600;">Completar registro</a>
        </p>
        <p style="margin:0 0 8px;line-height:1.6;color:#6b7280;">Si no solicitaste este registro, puedes ignorar este correo.</p>
    </div>
</body>
</html>
