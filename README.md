# App Estación Meteorológica

## Configuración de Base de Datos

1. Crear base de datos `app_estacion`
2. Ejecutar el script `database.sql`
3. Configurar credenciales en `models/Database.php`

## Configuración de Email

1. Crear cuenta Gmail para la aplicación
2. Habilitar autenticación de 2 factores
3. Generar contraseña de aplicación
4. Actualizar credenciales en `env.php`:
   - SMTP_USERNAME: tu-email@gmail.com
   - SMTP_PASSWORD: tu-app-password
   - FROM_EMAIL: tu-email@gmail.com

## Instalación PHPMailer

```bash
composer require phpmailer/phpmailer
```

## Funcionalidades Implementadas

### Autenticación
- ✅ Login con validación
- ✅ Registro de usuarios
- ✅ Activación por email
- ✅ Recuperación de contraseña
- ✅ Bloqueo de cuentas
- ✅ Reset de contraseña

### Estaciones
- ✅ Vista landing
- ✅ Panel de estaciones
- ✅ Detalle con gráficos (requiere login)
- ✅ Actualización automática cada minuto

### Seguridad
- ✅ Sesiones PHP
- ✅ Contraseñas hasheadas
- ✅ Tokens seguros
- ✅ Protección de rutas

## URLs Disponibles

- `/` - Landing page
- `/panel` - Lista de estaciones
- `/detalle/{chipid}` - Detalle de estación (requiere login)
- `/login` - Iniciar sesión
- `/register` - Registrarse
- `/recovery` - Recuperar contraseña
- `/reset/{token}` - Restablecer contraseña
- `/validate/{token}` - Activar cuenta
- `/blocked/{token}` - Bloquear cuenta
- `/logout` - Cerrar sesión