<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle - <?= APP_NAME ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; text-align: center; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 1rem; }
        .loading { text-align: center; padding: 3rem; font-size: 1.2rem; color: #666; }
        .estacion-info h2 { color: #333; margin-bottom: 1rem; font-size: 2rem; }
        .estacion-info p { color: #666; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .btn-back { background: #6c757d; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 25px; text-decoration: none; display: inline-block; margin-top: 1rem; }
        .btn-back:hover { background: #5a6268; }
        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-top: 2rem; }
        .chart-container { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .chart-title { font-size: 1.2rem; font-weight: bold; color: #333; margin-bottom: 1rem; text-align: center; }
        .chart-canvas { width: 100%; height: 250px; }
        .update-time { text-align: center; color: #666; font-size: 0.9rem; margin-top: 1rem; }
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
                <a href="?r=panel" class="btn-back">← Volver al Panel</a>
            </div>
        </div>
        
        <div class="charts-grid" id="chartsGrid" style="display: none;">
            <div class="chart-container">
                <div class="chart-title">🌡️ Temperatura</div>
                <canvas id="tempChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">💧 Humedad</div>
                <canvas id="humChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">💨 Viento</div>
                <canvas id="windChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">🌪️ Presión Atmosférica</div>
                <canvas id="pressChart" class="chart-canvas"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">🔥 Riesgo de Incendio</div>
                <canvas id="fireChart" class="chart-canvas"></canvas>
            </div>
        </div>
        
        <div class="update-time" id="updateTime" style="display: none;"></div>
    </div>

    <script>
        let charts = {};
        let currentStation = null;
        
        function generateRandomData(baseValue, variation = 5) {
            return baseValue + (Math.random() - 0.5) * variation;
        }
        
        function createCharts(estacion) {
            // Temperatura
            charts.temp = new Chart(document.getElementById('tempChart'), {
                type: 'line',
                data: {
                    labels: ['10 min', '8 min', '6 min', '4 min', '2 min', 'Ahora'],
                    datasets: [{
                        label: 'Temperatura (°C)',
                        data: [estacion.temperatura, estacion.temperatura + 0.5, estacion.temperatura - 0.3, estacion.temperatura + 0.2, estacion.temperatura - 0.1, estacion.temperatura],
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: estacion.temperatura - 5,
                            max: estacion.temperatura + 5
                        }
                    }
                }
            });
            
            // Humedad
            charts.hum = new Chart(document.getElementById('humChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Humedad', 'Sequedad'],
                    datasets: [{
                        data: [estacion.humedad, 100 - estacion.humedad],
                        backgroundColor: ['#4ecdc4', '#e8e8e8']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            
            // Viento
            charts.wind = new Chart(document.getElementById('windChart'), {
                type: 'bar',
                data: {
                    labels: ['Velocidad'],
                    datasets: [{
                        label: 'Viento (km/h)',
                        data: [estacion.viento],
                        backgroundColor: '#45b7d1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 30
                        }
                    }
                }
            });
            
            // Presión
            charts.press = new Chart(document.getElementById('pressChart'), {
                type: 'line',
                data: {
                    labels: ['10 min', '8 min', '6 min', '4 min', '2 min', 'Ahora'],
                    datasets: [{
                        label: 'Presión (hPa)',
                        data: [estacion.presion - 1, estacion.presion + 0.5, estacion.presion - 0.3, estacion.presion + 0.8, estacion.presion - 0.2, estacion.presion],
                        borderColor: '#96ceb4',
                        backgroundColor: 'rgba(150, 206, 180, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: estacion.presion - 5,
                            max: estacion.presion + 5
                        }
                    }
                }
            });
            
            // Riesgo de incendio
            const riskColors = ['#4ecdc4', '#feca57', '#ff9ff3', '#ff6b6b', '#ee5a24'];
            charts.fire = new Chart(document.getElementById('fireChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Riesgo Actual', 'Sin Riesgo'],
                    datasets: [{
                        data: [estacion.riesgo_incendio, 5 - estacion.riesgo_incendio],
                        backgroundColor: [riskColors[estacion.riesgo_incendio - 1], '#e8e8e8']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        function updateCharts(estacion) {
            // Actualizar temperatura
            const newTemp = generateRandomData(estacion.temperatura, 1);
            charts.temp.data.datasets[0].data.shift();
            charts.temp.data.datasets[0].data.push(newTemp);
            charts.temp.update();
            
            // Actualizar humedad
            const newHum = Math.max(0, Math.min(100, generateRandomData(estacion.humedad, 3)));
            charts.hum.data.datasets[0].data = [newHum, 100 - newHum];
            charts.hum.update();
            
            // Actualizar viento
            const newWind = Math.max(0, generateRandomData(estacion.viento, 2));
            charts.wind.data.datasets[0].data = [newWind];
            charts.wind.update();
            
            // Actualizar presión
            const newPress = generateRandomData(estacion.presion, 0.5);
            charts.press.data.datasets[0].data.shift();
            charts.press.data.datasets[0].data.push(newPress);
            charts.press.update();
            
            // Actualizar tiempo
            document.getElementById('updateTime').textContent = `Última actualización: ${new Date().toLocaleTimeString()}`;
        }
        
        async function cargarDetalle() {
            const chipid = '<?= $chipid ?>';
            
            try {
                const apiUrl = window.location.href.includes('?') 
                    ? window.location.href.split('?')[0] + 'api.php'
                    : window.location.href + 'api.php';
                const response = await fetch(apiUrl);
                const estaciones = await response.json();
                
                const estacion = estaciones.find(e => e.chipid === chipid);
                
                if (estacion) {
                    currentStation = estacion;
                    document.getElementById('apodo').textContent = estacion.apodo;
                    document.getElementById('ubicacion').textContent = estacion.ubicacion;
                    
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('estacionInfo').style.display = 'block';
                    document.getElementById('chartsGrid').style.display = 'grid';
                    document.getElementById('updateTime').style.display = 'block';
                    
                    createCharts(estacion);
                    document.getElementById('updateTime').textContent = `Última actualización: ${new Date().toLocaleTimeString()}`;
                    
                    // Actualizar cada minuto
                    setInterval(() => {
                        updateCharts(currentStation);
                    }, 60000);
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