<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle - <?= APP_NAME ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .header { background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); color: white; padding: 2rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .header h1 { font-size: 2.5rem; margin-bottom: 0.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.1); margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.2); }
        .loading { text-align: center; padding: 3rem; font-size: 1.2rem; color: #666; }
        .estacion-info h2 { color: #333; margin-bottom: 1rem; font-size: 2.2rem; font-weight: 600; }
        .estacion-info p { color: #555; font-size: 1.1rem; margin-bottom: 0.5rem; }
        .btn-back { background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 0.8rem 2rem; border: none; border-radius: 30px; text-decoration: none; display: inline-block; margin-top: 1rem; font-weight: 500; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4); }
        .charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-top: 2rem; }
        .chart-container { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; padding: 1.5rem; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s; }
        .chart-container:hover { transform: translateY(-5px); box-shadow: 0 12px 40px rgba(0,0,0,0.15); }
        .chart-title { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 1rem; text-align: center; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .chart-canvas { position: relative; height: 200px !important; width: 100% !important; max-height: 200px; }
        .chart-container:last-child { grid-column: 1 / -1; max-width: 450px; margin: 0 auto; }
        @media (max-width: 768px) {
            .charts-grid { grid-template-columns: 1fr; gap: 1rem; }
            .chart-container:last-child { grid-column: 1; }
            .header h1 { font-size: 2rem; }
        }
        .update-time { text-align: center; color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-top: 1rem; background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; backdrop-filter: blur(10px); }
        .status-indicator { display: inline-block; width: 8px; height: 8px; background: #4ecdc4; border-radius: 50%; margin-right: 0.5rem; animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
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
                <div style="height: 200px; position: relative;">
                    <canvas id="tempChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-title">💧 Humedad</div>
                <div style="height: 200px; position: relative;">
                    <canvas id="humChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-title">💨 Viento</div>
                <div style="height: 200px; position: relative;">
                    <canvas id="windChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-title">🌪️ Presión Atmosférica</div>
                <div style="height: 200px; position: relative;">
                    <canvas id="pressChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-title">🔥 Riesgo de Incendio</div>
                <div style="height: 200px; position: relative;">
                    <canvas id="fireChart" width="400" height="200"></canvas>
                </div>
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
                    responsive: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: estacion.temperatura - 3,
                            max: estacion.temperatura + 3
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
                    responsive: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 10 } }
                        }
                    }
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
                    responsive: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 30 }
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
                    responsive: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: estacion.presion - 3,
                            max: estacion.presion + 3
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
                    responsive: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 10 } }
                        }
                    }
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
            document.getElementById('updateTime').innerHTML = `<span class="status-indicator"></span>Última actualización: ${new Date().toLocaleTimeString()}`;
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
                    document.getElementById('updateTime').innerHTML = `<span class="status-indicator"></span>Última actualización: ${new Date().toLocaleTimeString()}`;
                    
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