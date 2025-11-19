<?php
class TemplateEngine {
    private $templateDir;
    private $data = [];
    
    public function __construct($templateDir = 'views/') {
        $this->templateDir = $templateDir;
    }
    
    public function assign($key, $value) {
        $this->data[$key] = $value;
    }
    
    public function render($template, $data = []) {
        $templatePath = $this->templateDir . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template not found: $templatePath");
        }
        
        $mergedData = array_merge($this->data, $data);
        extract($mergedData);
        
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
    
    public function display($template, $data = []) {
        echo $this->render($template, $data);
    }
}