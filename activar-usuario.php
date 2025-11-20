<?php
require_once 'models/Database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $email = 'liolioclem@gmail.com';
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM EstacionUsuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Usuario encontrado:<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Nombres: " . $user['nombres'] . "<br>";
        echo "Activo: " . ($user['activo'] ? 'SÍ' : 'NO') . "<br>";
        
        if (!$user['activo']) {
            // Activate user
            $stmt = $pdo->prepare("UPDATE EstacionUsuarios SET activo = 1, token_action = NULL, active_date = NOW() WHERE email = ?");
            $result = $stmt->execute([$email]);
            
            if ($result) {
                echo "<br>✅ Usuario activado exitosamente<br>";
                echo "<a href='?r=login'>Ir al login</a>";
            } else {
                echo "<br>❌ Error al activar usuario";
            }
        } else {
            echo "<br>✅ Usuario ya está activo<br>";
            echo "<a href='?r=login'>Ir al login</a>";
        }
    } else {
        echo "❌ Usuario no encontrado";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>