<?php
namespace BillingSystem\Core;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            $this->start();
        }
    }

    public function start() {
        session_start();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function clear() {
        session_unset();
    }

    public function destroy() {
        session_destroy();
    }

    public function regenerate() {
        session_regenerate_id(true);
    }

    public function flash($key, $message = null) {
        if ($message === null) {
            $message = $this->get($key);
            $this->remove($key);
            return $message;
        }
        
        $this->set($key, $message);
    }

    public function hasFlash($key) {
        return $this->has($key);
    }

    public function getFlash($key, $default = null) {
        return $this->flash($key) ?? $default;
    }
} 