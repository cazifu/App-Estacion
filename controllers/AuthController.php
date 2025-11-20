<?php
require_once 'models/User.php';
require_once 'models/EmailService.php';

class AuthController {
    private $template;
    private $user;
    private $emailService;
    
    public function __construct($template) {
        $this->template = $template;
        
        try {
            $this->user = new User();
            file_put_contents('emails/register_debug.txt', "User model created successfully\n", FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('emails/register_debug.txt', "User model error: " . $e->getMessage() . "\n", FILE_APPEND);
        }
        
        try {
            $this->emailService = new EmailService();
        } catch (Exception $e) {
            // Log error if EmailService fails to load
            if (!is_dir('emails')) {
                mkdir('emails', 0777, true);
            }
            file_put_contents('emails/constructor_error.txt', "EmailService error: " . $e->getMessage() . "\n", FILE_APPEND);
            $this->emailService = null;
        }
    }
    
    public function login() {
        if ($this->isLoggedIn()) {
            header('Location: ?r=panel');
            exit;
        }
        
        if ($_POST) {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->user->findByEmail($email);
            
            if ($user && password_verify($password, $user['contraseña'])) {
                if (!$user['activo']) {
                    $message = 'Su usuario aún no se ha validado, revise su casilla de correo';
                } elseif ($user['bloqueado'] || $user['recupero']) {
                    $message = 'Su usuario está bloqueado, revise su casilla de correo';
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_token'] = $user['token'];
                    // Enviar email de notificación de login
                    header('Location: ?r=panel');
                    exit;
                }
            } else {
                if ($user) {
                    // Enviar email de intento de acceso inválido
                }
                $message = 'Credenciales no válidas';
            }
        }
        
        echo $this->template->render('login', ['message' => $message ?? '']);
    }
    
    public function register() {
        // Debug: Always create debug file to see if method is called
        if (!is_dir('emails')) {
            mkdir('emails', 0777, true);
        }
        file_put_contents('emails/register_debug.txt', "Register method called at: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        
        if ($this->isLoggedIn()) {
            header('Location: ?r=panel');
            exit;
        }
        
        if ($_POST) {
            file_put_contents('emails/register_debug.txt', "POST data received\n", FILE_APPEND);
            $email = $_POST['email'] ?? '';
            $nombres = $_POST['nombres'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            
            file_put_contents('emails/register_debug.txt', "Email: {$email}, Nombres: {$nombres}\n", FILE_APPEND);
            
            if ($password !== $confirm) {
                file_put_contents('emails/register_debug.txt', "Password mismatch\n", FILE_APPEND);
                $message = 'Las contraseñas no coinciden';
            } elseif ($this->user->findByEmail($email)) {
                file_put_contents('emails/register_debug.txt', "Email already exists\n", FILE_APPEND);
                $message = 'El email ya está registrado';
            } else {
                file_put_contents('emails/register_debug.txt', "Attempting to create user: {$email}\n", FILE_APPEND);
                
                if ($this->user->create($email, $nombres, $password)) {
                    file_put_contents('emails/register_debug.txt', "User created successfully\n", FILE_APPEND);
                    $user = $this->user->findByEmail($email);
                    file_put_contents('emails/register_debug.txt', "User found: " . ($user ? 'YES' : 'NO') . "\n", FILE_APPEND);
                    
                    // Debug log
                    $debugLog = "Intentando enviar email:\n";
                    $debugLog .= "Email: {$email}\n";
                    $debugLog .= "Nombres: {$nombres}\n";
                    $debugLog .= "Token action: " . ($user['token_action'] ?? 'NULL') . "\n";
                    $debugLog .= "Fecha: " . date('Y-m-d H:i:s') . "\n\n";
                    
                    if (!is_dir('emails')) {
                        mkdir('emails', 0777, true);
                    }
                    file_put_contents('emails/debug_log.txt', $debugLog, FILE_APPEND);
                    
                    if ($user && $user['token_action']) {
                        if ($this->emailService && $this->emailService->sendActivationEmail($email, $nombres, $user['token_action'])) {
                            $message = 'Usuario registrado. Revise su correo para activar la cuenta';
                        } else {
                            $message = 'Usuario registrado pero error al enviar email. Contacte soporte.';
                        }
                    } else {
                        $message = 'Usuario registrado pero error al generar token de activación';
                    }
                } else {
                    file_put_contents('emails/register_debug.txt', "Failed to create user\n", FILE_APPEND);
                    $message = 'Error al registrar usuario';
                }
            }
        }
        
        echo $this->template->render('register', ['message' => $message ?? '']);
    }
    
    public function validate($token_action) {
        if ($this->isLoggedIn()) {
            header('Location: ?r=panel');
            exit;
        }
        
        $user = $this->user->findByTokenAction($token_action);
        if ($user && !$user['activo']) {
            $this->user->activate($token_action);
            // Enviar email de confirmación
            header('Location: ?r=login');
            exit;
        }
        
        echo $this->template->render('message', ['message' => 'El token no corresponde a un usuario']);
    }
    
    public function blocked($token) {
        $user = $this->user->findByToken($token);
        if ($user) {
            $this->user->block($token);
            // Enviar email de bloqueo
            echo $this->template->render('message', ['message' => 'Usuario bloqueado, revise su correo electrónico']);
        } else {
            echo $this->template->render('message', ['message' => 'El token no corresponde a un usuario']);
        }
    }
    
    public function recovery() {
        if ($this->isLoggedIn()) {
            header('Location: ?r=panel');
            exit;
        }
        
        if ($_POST) {
            $email = $_POST['email'] ?? '';
            $user = $this->user->findByEmail($email);
            
            if ($user) {
                $this->user->setRecovery($email);
                // Enviar email de recuperación
                $message = 'Se ha enviado un email para restablecer la contraseña';
            } else {
                $message = 'El email no se encuentra registrado';
            }
        }
        
        echo $this->template->render('recovery', ['message' => $message ?? '']);
    }
    
    public function reset($token_action) {
        if ($this->isLoggedIn()) {
            header('Location: ?r=panel');
            exit;
        }
        
        $user = $this->user->findByTokenAction($token_action);
        if (!$user) {
            echo $this->template->render('message', ['message' => 'Token inválido']);
            return;
        }
        
        if ($_POST) {
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            
            if ($password === $confirm) {
                $this->user->resetPassword($token_action, $password);
                // Enviar email de confirmación
                header('Location: ?r=login');
                exit;
            } else {
                $message = 'Las contraseñas no coinciden';
            }
        }
        
        echo $this->template->render('reset', ['message' => $message ?? '', 'token_action' => $token_action]);
    }
    
    public function logout() {
        session_destroy();
        header('Location: ?r=login');
        exit;
    }
    
    private function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}