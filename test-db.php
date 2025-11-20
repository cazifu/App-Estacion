<?php
// Test database connection
try {
    $host = 'mattprofe.com.ar';
    $dbname = '9870';
    $username = '9870';
    $password = 'conejo.alamo.auto';
    
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión a base de datos exitosa<br>";
    
    // Test if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'EstacionUsuarios'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla EstacionUsuarios existe<br>";
        
        // Test insert
        $token = bin2hex(random_bytes(32));
        $token_action = bin2hex(random_bytes(32));
        $hashedPassword = password_hash('test123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO EstacionUsuarios (token, email, nombres, contraseña, token_action) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$token, 'test@test.com', 'Test User', $hashedPassword, $token_action]);
        
        if ($result) {
            echo "✅ Insert test exitoso<br>";
            echo "Token de activación: {$token_action}<br>";
            
            // Clean up test data
            $pdo->prepare("DELETE FROM EstacionUsuarios WHERE email = 'test@test.com'")->execute();
            echo "✅ Test data cleaned<br>";
        } else {
            echo "❌ Error en insert test<br>";
        }
        
    } else {
        echo "❌ Tabla EstacionUsuarios no existe<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "<br>";
}
?>