<?php

namespace App\Routes;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$this->normalizePath($path)] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$this->normalizePath($path)] = $callback;
    }

    private function normalizePath($path)
    {
        // Đảm bảo đường dẫn bắt đầu bằng / và không có / ở cuối
        $path = '/' . trim($path, '/');
        return $path;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = $this->normalizePath($uri);

        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            // Thay thế {id} hoặc {param} thành biểu thức chính quy
            $routePattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $route);
            $routePattern = "#^" . $routePattern . "$#";

            if (preg_match($routePattern, $uri, $matches)) {
                array_shift($matches); // Bỏ phần khớp toàn bộ
                try {
                    if (is_array($callback) && count($callback) === 2) {
                        $class = $callback[0];
                        $method = $callback[1];
                        if (!class_exists($class)) {
                            throw new \Exception("Controller class $class not found");
                        }
                        $controller = new $class();
                        if (!method_exists($controller, $method)) {
                            throw new \Exception("Method $method not found in $class");
                        }
                        call_user_func_array([$controller, $method], $matches);
                    } else {
                        call_user_func_array($callback, $matches);
                    }
                } catch (\Exception $e) {
                    http_response_code(500);
                    echo "Server Error: " . $e->getMessage();
                    return;
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
