<?php
namespace BillingSystem\Core;

class Router {
    private $routes = [];
    private $params = [];
    private $notFoundCallback;

    public function add($route, $params = []) {
        // Convert route to regex pattern
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    public function match($url) {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatch() {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                if (method_exists($controller_object, $action)) {
                    return $controller_object->$action();
                } else {
                    throw new \Exception("Method $action in controller $controller not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        }

        if ($this->notFoundCallback) {
            return call_user_func($this->notFoundCallback);
        }

        throw new \Exception('No route matched.', 404);
    }

    protected function removeQueryStringVariables($url) {
        if ($url != '') {
            $parts = explode('?', $url, 2);
            $url = $parts[0];
        }
        return $url;
    }

    protected function getNamespace() {
        $namespace = 'BillingSystem\\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }
} 