<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emails Enviados - Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .email { border: 1px solid #ccc; margin: 10px 0; padding: 15px; border-radius: 5px; }
        .email-header { background: #f5f5f5; padding: 10px; margin: -15px -15px 10px -15px; }
        .email-content { background: white; }
        a { color: #007cba; }
    </style>
</head>
<body>
    <h1>📧 Emails Enviados (Modo Test)</h1>
    
    <?php
    $emailFiles = glob('emails/*.html');
    
    if (empty($emailFiles)) {
        echo '<p>No hay emails enviados aún.</p>';
    } else {
        // Ordenar por fecha (más reciente primero)
        usort($emailFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        foreach ($emailFiles as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            $to = '';
            $subject = '';
            $from = '';
            $body = '';
            $bodyStart = false;
            
            foreach ($lines as $line) {
                if (strpos($line, 'To: ') === 0) {
                    $to = substr($line, 4);
                } elseif (strpos($line, 'Subject: ') === 0) {
                    $subject = substr($line, 9);
                } elseif (strpos($line, 'From: ') === 0) {
                    $from = substr($line, 6);
                } elseif (empty(trim($line)) && !$bodyStart) {
                    $bodyStart = true;
                } elseif ($bodyStart) {
                    $body .= $line . "\n";
                }
            }
            
            echo '<div class="email">';
            echo '<div class="email-header">';
            echo '<strong>Para:</strong> ' . htmlspecialchars($to) . '<br>';
            echo '<strong>De:</strong> ' . htmlspecialchars($from) . '<br>';
            echo '<strong>Asunto:</strong> ' . htmlspecialchars($subject) . '<br>';
            echo '<strong>Fecha:</strong> ' . date('Y-m-d H:i:s', filemtime($file));
            echo '</div>';
            echo '<div class="email-content">' . $body . '</div>';
            echo '</div>';
        }
    }
    ?>
    
    <p><a href="index.php">← Volver a la aplicación</a></p>
</body>
</html>