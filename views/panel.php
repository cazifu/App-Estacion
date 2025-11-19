<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - <?= APP_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; text-align: center; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .loading { text-align: center; padding: 3rem; font-size: 1.2rem; color: #666; }
        .estaciones-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .estacion-card { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s; border: none; text-align: left; width: 100%; }
        .estacion-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .estacion-apodo { font-size: 1.3rem; font-weight: bold; color: #333; margin-bottom: 0.5rem; }
        .estacion-ubicacion { color: #666; margin-bottom: 1rem; }
        .estacion-visitas { background: #e3f2fd; color: #1976d2; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; display: inline-block; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌤️ Estaciones Meteorológicas</h1>
        <p>Selecciona una estación para ver sus detalles</p>
    </div>
    
    <div class="container">
        <div class="loading" id="loading">Cargando estaciones...</div>
        <div class="estaciones-grid" id="estacionesGrid" style="display: none;"></div>
    </div>

    <template id="estacionTemplate">
        <button class="estacion-card" onclick="verDetalle(this.dataset.chipid)">
            <div class="estacion-apodo"></div>
            <div class="estacion-ubicacion">📍 </div>
            <div class="estacion-visitas">👁️ visitas</div>
        </button>
    </template>

    <script>
        async function cargarEstaciones() {
            try {
                const response = await fetch('<?= API_URL ?>');
                const estaciones = await response.json();
                
                const grid = document.getElementById('estacionesGrid');
                const template = document.getElementById('estacionTemplate');
                const loading = document.getElementById('loading');
                
                estaciones.forEach(estacion => {
                    const clone = template.content.cloneNode(true);
                    const card = clone.querySelector('.estacion-card');
                    
                    card.dataset.chipid = estacion.chipid;
                    clone.querySelector('.estacion-apodo').textContent = estacion.apodo;
                    clone.querySelector('.estacion-ubicacion').textContent = `📍 ${estacion.ubicacion}`;
                    clone.querySelector('.estacion-visitas').textContent = `👁️ ${estacion.visitas} visitas`;
                    
                    grid.appendChild(clone);
                });
                
                loading.style.display = 'none';
                grid.style.display = 'grid';
            } catch (error) {
                document.getElementById('loading').textContent = 'Error al cargar las estaciones';
            }
        }
        
        function verDetalle(chipid) {
            window.location.href = `detalle/${chipid}`;
        }
        
        cargarEstaciones();
    </script>
</body>
</html>