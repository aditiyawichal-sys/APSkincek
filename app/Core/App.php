<?php
class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Check if it's admin route
        if(isset($url[0]) && strtolower($url[0]) == 'admin') {
            array_shift($url); // remove 'admin'
            $controllerName = isset($url[0]) ? ucwords($url[0]) : 'Dashboard';
            if (file_exists('../app/Controllers/Admin/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
                require_once '../app/Controllers/Admin/' . $this->controller . '.php';
            } else {
                // Default admin controller
                if(file_exists('../app/Controllers/Admin/Dashboard.php')) {
                    require_once '../app/Controllers/Admin/Dashboard.php';
                    $this->controller = 'Dashboard';
                }
            }
        } else {
            // Customer routes
            $controllerName = isset($url[0]) ? ucwords($url[0]) : 'Home';
            if (file_exists('../app/Controllers/Customer/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
            
            if(file_exists('../app/Controllers/Customer/' . $this->controller . '.php')){
                require_once '../app/Controllers/Customer/' . $this->controller . '.php';
            }
        }

        if(class_exists($this->controller)){
            $this->controller = new $this->controller;

            if (isset($url[1])) {
                if (method_exists($this->controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                }
            }

            $this->params = $url ? array_values($url) : [];
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            echo "Controller not found.";
        }
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
