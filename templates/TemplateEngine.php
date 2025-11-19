<?php
class TemplateEngine {
    private $templateDir;
    
    public function __construct($templateDir = 'views/') {
        $this->templateDir = $templateDir;
    }
    
    public function render($template, $data = []) {
        $templatePath = $this->templateDir . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template not found: $templatePath");
        }
        
        extract($data);
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}