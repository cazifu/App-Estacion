<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensaje - <?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.1); width: 100%; max-width: 400px; border: 1px solid rgba(255,255,255,0.2); text-align: center; }
        h1 { color: #333; margin-bottom: 1.5rem; font-size: 2rem; }
        .message { color: #555; font-size: 1.1rem; margin-bottom: 2rem; }
        .btn { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 0.8rem 2rem; border: none; border-radius: 10px; font-size: 1rem; text-decoration: none; display: inline-block; transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌤️ Mensaje</h1>
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <a href="?r=login" class="btn">Ir al Login</a>
    </div>
</body>
</html>