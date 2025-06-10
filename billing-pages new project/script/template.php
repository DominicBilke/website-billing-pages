<?php
require_once 'config.php';
require_once 'auth.php';

class Template {
    private static $instance = null;
    private $auth;
    private $pageTitle = '';
    private $breadcrumbs = [];
    private $scripts = [];
    private $styles = [];
    private $content = '';
    private $messages = [];
    
    private function __construct() {
        $this->auth = Auth::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function setPageTitle($title) {
        $this->pageTitle = $title;
        return $this;
    }
    
    public function addBreadcrumb($label, $url = '') {
        $this->breadcrumbs[] = [
            'label' => $label,
            'url' => $url
        ];
        return $this;
    }
    
    public function addScript($src) {
        $this->scripts[] = $src;
        return $this;
    }
    
    public function addStyle($href) {
        $this->styles[] = $href;
        return $this;
    }
    
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function addMessage($message, $type = 'info') {
        $this->messages[] = [
            'message' => $message,
            'type' => $type
        ];
        return $this;
    }
    
    public function render($template = 'base') {
        $templateFile = APP_ROOT . "/templates/{$template}.php";
        
        if (!file_exists($templateFile)) {
            throw new Exception("Template file not found: {$template}");
        }
        
        $user = $this->auth->getCurrentUser();
        
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
    
    public function renderBreadcrumbs() {
        if (empty($this->breadcrumbs)) {
            return '';
        }
        
        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        
        foreach ($this->breadcrumbs as $index => $crumb) {
            $isLast = $index === count($this->breadcrumbs) - 1;
            
            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($crumb['label']) . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['label']) . '</a></li>';
            }
        }
        
        $html .= '</ol></nav>';
        return $html;
    }
    
    public function renderMessages() {
        if (empty($this->messages)) {
            return '';
        }
        
        $html = '';
        foreach ($this->messages as $message) {
            $html .= '<div class="alert alert-' . htmlspecialchars($message['type']) . ' alert-dismissible fade show" role="alert">';
            $html .= htmlspecialchars($message['message']);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $html .= '</div>';
        }
        
        return $html;
    }
    
    public function renderScripts() {
        if (empty($this->scripts)) {
            return '';
        }
        
        $html = '';
        foreach ($this->scripts as $src) {
            $html .= '<script src="' . htmlspecialchars($src) . '"></script>';
        }
        
        return $html;
    }
    
    public function renderStyles() {
        if (empty($this->styles)) {
            return '';
        }
        
        $html = '';
        foreach ($this->styles as $href) {
            $html .= '<link rel="stylesheet" href="' . htmlspecialchars($href) . '">';
        }
        
        return $html;
    }
    
    public function getPageTitle() {
        return $this->pageTitle;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function getUser() {
        return $this->auth->getCurrentUser();
    }
    
    public function isLoggedIn() {
        return $this->auth->isLoggedIn();
    }
    
    public function hasRole($role) {
        $user = $this->getUser();
        return $user && $user['role'] === $role;
    }
}

// Initialize template
$template = Template::getInstance(); 