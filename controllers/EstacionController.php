<?php
class EstacionController {
    private $template;
    
    public function __construct($template) {
        $this->template = $template;
    }
    
    public function landing() {
        echo $this->template->render('landing');
    }
    
    public function panel() {
        echo $this->template->render('panel');
    }
    
    public function detalle($chipid) {
        echo $this->template->render('detalle', ['chipid' => $chipid]);
    }
}