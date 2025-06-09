<?php
namespace BillingSystem\Core;

abstract class Controller {
    protected $app;
    protected $db;
    protected $auth;
    protected $template;
    protected $session;
    protected $params;

    public function __construct($params = []) {
        $this->app = Application::getInstance();
        $this->db = $this->app->getDb();
        $this->auth = $this->app->getAuth();
        $this->template = $this->app->getTemplate();
        $this->session = $this->app->getSession();
        $this->params = $params;
    }

    protected function requireLogin() {
        if (!$this->auth->isLoggedIn()) {
            $this->session->flash('error', 'Please log in to access this page.');
            header('Location: /login.php');
            exit;
        }
    }

    protected function requireRole($role) {
        $this->requireLogin();
        if (!$this->auth->hasRole($role)) {
            $this->session->flash('error', 'You do not have permission to access this page.');
            header('Location: /unauthorized.php');
            exit;
        }
    }

    protected function render($view, $data = []) {
        $this->template->setContent($view);
        foreach ($data as $key => $value) {
            $this->template->set($key, $value);
        }
        return $this->template->render();
    }

    protected function json($data) {
        header('Content-Type: application/json');
        return json_encode($data);
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function validate($data, $rules) {
        $errors = [];
        foreach ($rules as $field => $rule) {
            if (strpos($rule, 'required') !== false && empty($data[$field])) {
                $errors[$field] = "The $field field is required.";
            }
            if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "The $field must be a valid email address.";
            }
            if (strpos($rule, 'min:') !== false) {
                preg_match('/min:(\d+)/', $rule, $matches);
                $min = $matches[1];
                if (strlen($data[$field]) < $min) {
                    $errors[$field] = "The $field must be at least $min characters.";
                }
            }
        }
        return $errors;
    }
} 