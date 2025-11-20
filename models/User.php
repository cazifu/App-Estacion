<?php
require_once 'Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($email, $nombres, $password) {
        $token = bin2hex(random_bytes(32));
        $token_action = bin2hex(random_bytes(32));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->getConnection()->prepare("INSERT INTO EstacionUsuarios (token, email, nombres, contraseña, token_action) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$token, $email, $nombres, $hashedPassword, $token_action]);
    }
    
    public function findByEmail($email) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM EstacionUsuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByToken($token) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM EstacionUsuarios WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByTokenAction($token_action) {
        $stmt = $this->db->getConnection()->prepare("SELECT * FROM EstacionUsuarios WHERE token_action = ?");
        $stmt->execute([$token_action]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function activate($token_action) {
        $stmt = $this->db->getConnection()->prepare("UPDATE EstacionUsuarios SET activo = 1, token_action = NULL, active_date = NOW() WHERE token_action = ?");
        return $stmt->execute([$token_action]);
    }
    
    public function block($token) {
        $token_action = bin2hex(random_bytes(32));
        $stmt = $this->db->getConnection()->prepare("UPDATE EstacionUsuarios SET bloqueado = 1, token_action = ?, blocked_date = NOW() WHERE token = ?");
        return $stmt->execute([$token_action, $token]);
    }
    
    public function setRecovery($email) {
        $token_action = bin2hex(random_bytes(32));
        $stmt = $this->db->getConnection()->prepare("UPDATE EstacionUsuarios SET recupero = 1, token_action = ?, recover_date = NOW() WHERE email = ?");
        return $stmt->execute([$token_action, $email]);
    }
    
    public function resetPassword($token_action, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->getConnection()->prepare("UPDATE EstacionUsuarios SET contraseña = ?, token_action = NULL, bloqueado = 0, recupero = 0 WHERE token_action = ?");
        return $stmt->execute([$hashedPassword, $token_action]);
    }
}