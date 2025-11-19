<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle - <?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; text-align: center; }
        .container { max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .loading { text-align: center; padding: 3rem; font-size: 1.2rem; color: #666; }
        .estacion-info h2 { color: #333; margin-bottom: 1rem; font-size: 2rem; }
        .estacion-info p { color: #666; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .btn-back { background: #6c757d; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 25px; text-decoration: none; display: inline-block; margin-top: 1rem; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌤️ Detalle de Estación</h1>
    </div>
    
    <div class="container">
        <div class="card">
            <div class="loading" id="loading">Cargando información...</div>
            <div class="estacion-info" id="estacionInfo" style="display: none;">
                <h2 id="apodo"></h2>
                <p><strong>📍 Ubicación:</strong> <span id="ubicacion"></span></p>
                <a href="panel" class="btn-back">← Volver al Panel</a>
            </div>
        </div>
    </div>

    <script>
        async function cargarDetalle() {
            const chipid = '<?= $chipid ?>';
            
            try {
                const response = await fetch('<?= API_URL ?>');
                const estaciones = await response.json();
                
                const estacion = estaciones.find(e => e.chipid === chipid);
                
                if (estacion) {
                    document.getElementById('apodo').textContent = estacion.apodo;
                    document.getElementById('ubicacion').textContent = estacion.ubicacion;
                    
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('estacionInfo').style.display = 'block';
                } else {
                    document.getElementById('loading').textContent = 'Estación no encontrada';
                }
            } catch (error) {
                document.getElementById('loading').textContent = 'Error al cargar la información';
            }
        }
        
        cargarDetalle();
    </script>
</body>
</html>