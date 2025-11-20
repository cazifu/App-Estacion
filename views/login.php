<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.1); width: 100%; max-width: 400px; border: 1px solid rgba(255,255,255,0.2); }
        h1 { color: #333; margin-bottom: 1.5rem; text-align: center; font-size: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; font-weight: 500; }
        input { width: 100%; padding: 0.8rem; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s; }
        input:focus { outline: none; border-color: #667eea; }
        .btn { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 0.8rem 2rem; border: none; border-radius: 10px; font-size: 1rem; cursor: pointer; width: 100%; margin: 1rem 0; transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
        .links { text-align: center; margin-top: 1rem; }
        .links a { color: #667eea; text-decoration: none; display: block; margin: 0.5rem 0; }
        .links a:hover { text-decoration: underline; }
        .message { background: #f8d7da; color: #721c24; padding: 0.8rem; border-radius: 10px; margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌤️ Iniciar Sesión</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Acceder</button>
        </form>
        
        <div class="links">
            <a href="?r=recovery">¿Olvidaste tu contraseña?</a>
            <a href="?r=register">¿No tienes una cuenta? Registrarse</a>
        </div>
    </div>
</body>
</html>