<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
        h1 { color: #333; margin-bottom: 1rem; font-size: 2.5rem; }
        p { color: #666; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.6; }
        .btn { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 1rem 2rem; border: none; border-radius: 50px; font-size: 1.1rem; cursor: pointer; text-decoration: none; display: inline-block; transition: transform 0.3s; }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌤️ Estaciones Meteorológicas</h1>
        <p>Monitorea en tiempo real las condiciones climáticas de diferentes estaciones meteorológicas. Accede a datos actualizados de temperatura, humedad y más.</p>
        <a href="?r=panel" class="btn">Ver Estaciones</a>
    </div>
</body>
</html>