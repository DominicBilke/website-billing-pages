<?php
namespace BillingSystem\Core;

class Application {
    private static $instance = null;
    private $config;
    private $db;
    private $auth;
    private $template;
    private $router;
    private $session;

    private function __construct() {
        // Load configuration
        $this->config = require __DIR__ . '/../../config/config.php';
        
        // Initialize components
        $this->initSession();
        $this->initDatabase();
        $this->initAuth();
        $this->initTemplate();
        $this->initRouter();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initSession() {
        $this->session = new Session();
        $this->session->start();
    }

    private function initDatabase() {
        $this->db = new Database(
            $this->config['db_host'],
            $this->config['db_name'],
            $this->config['db_user'],
            $this->config['db_pass']
        );
    }

    private function initAuth() {
        $this->auth = new Auth($this->db, $this->session);
    }

    private function initTemplate() {
        $this->template = new Template();
    }

    private function initRouter() {
        $this->router = new Router();
    }

    public function run() {
        try {
            // Handle the request
            $response = $this->router->dispatch();
            
            // Send the response
            if (is_string($response)) {
                echo $response;
            } else {
                $response->send();
            }
        } catch (\Exception $e) {
            // Handle errors
            $this->handleError($e);
        }
    }

    private function handleError(\Exception $e) {
        if ($this->config['debug']) {
            // Show detailed error in debug mode
            echo '<h1>Error</h1>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
        } else {
            // Show generic error in production
            $this->template->setPageTitle('Error');
            $this->template->setContent('
                <div class="alert alert-danger">
                    An error occurred. Please try again later.
                </div>
            ');
            echo $this->template->render();
        }
    }

    public function getConfig() {
        return $this->config;
    }

    public function getDb() {
        return $this->db;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getSession() {
        return $this->session;
    }
} 