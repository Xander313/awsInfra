<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credenciales de Acceso - SGPD COAC</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7fa; margin: 0; padding: 0;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
                    <tr>
                        <td align="center" style="background-color: #1e40af; padding: 30px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">SGPD COAC</h1>
                            <p style="color: #bfdbfe; margin: 10px 0 0 0; font-size: 14px;">Sistema de Gestión de Protección de Datos</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            @if(isset($tipo) && $tipo == 'bienvenida')
                                <h2 style="color: #1f2937; margin-top: 0;">¡Bienvenido/a, {{ $user->full_name }}!</h2>
                                <p style="color: #4b5563; line-height: 1.6;">Su cuenta ha sido creada exitosamente en nuestra plataforma.</p>
                            @else
                                <h2 style="color: #1f2937; margin-top: 0;">Actualización de Credenciales</h2>
                                <p style="color: #4b5563; line-height: 1.6;">Hola {{ $user->full_name }}, su contraseña de acceso ha sido actualizada por un administrador.</p>
                            @endif

                            <p style="color: #4b5563; line-height: 1.6;">A continuación encontrará sus credenciales de acceso:</p>

                            <div style="background-color: #f3f4f6; border-left: 4px solid #3b82f6; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Correo Electrónico</p>
                                <p style="margin: 0 0 20px 0; color: #111827; font-weight: bold; font-size: 16px;">{{ $user->email }}</p>
                                
                                
                            <div style="background-color: #f3f4f6; border-left: 4px solid #3b82f6; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="color: #4b5563; margin-bottom: 15px; font-size: 14px;">Por seguridad, su contraseña no se incluye en este mensaje. Use el botón inferior para visualizarla:</p>
                                
                                <a href="{{ $revealUrl }}" style="background-color: #1e40af; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                                    Revelar Contraseña
                                </a>
                                
                                <p style="color: #9ca3af; font-size: 11px; margin-top: 15px;">
                                    * Este enlace es de un solo uso y expirará en 20 minutos por seguridad.
                                </p>
                            </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 20px; border-top: 1px solid #e5e7eb;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">Este es un mensaje automático, por favor no responda a este correo.</p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 5px 0 0 0;">&copy; {{ date('Y') }} SGPD COAC. Todos los derechos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>