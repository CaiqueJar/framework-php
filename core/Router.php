<?php

namespace core;

use Closure;
use Exception;

class Router {

    private static $routes = [];

    public static function load() {
        $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $request_method = $_SERVER['REQUEST_METHOD'];

        
        $route_found = current(array_filter(self::$routes, function ($route) use($url_path, $request_method) {
            $uri_parts = explode('/', trim($url_path, '/'));
            $uri_parts_obj = explode('/', trim($route->uri, '/'));

            $parameters_matched = array();

            foreach ($uri_parts_obj as $key => $value) {
                if (strpos($value, '{') !== false && strpos($value, '}') !== false && isset($uri_parts[$key])) {
                    $parameters_matched[$value] = $uri_parts[$key];
                }
            }

            if(self::verifyRouteMatch($uri_parts_obj, $uri_parts, $route->method, $request_method)) {
                $route->parameters = $parameters_matched;
                return $route;
            }
        }));        


        if(empty($route_found)) {
            throw new \Exception('Rota não existe');
        }

        if($route_found->action instanceof Closure) {
            $route_found->action->__invoke();
            return;
        }
        
        $controller = $route_found->action[0];
        $method = $route_found->action[1];

        
        if(!class_exists($controller)) {
            throw new \Exception('Controller não existe');
        }

        $controller_obj = new $controller;

        if(!method_exists($controller_obj, $method)) {
            throw new \Exception('Método não existe');
        }

        return call_user_func_array([$controller_obj, $method], array_values($route_found->parameters));
    }

    private static function verifyParameters($uri) {
        $parameters = [];
        $result = preg_match_all('/\{([^}]*)\}/', $uri, $parameters);

        return $result == 0 ? [] : $parameters[1];
    }

    private static function verifyRouteMatch($route_parts, $url_parts, $route_method, $actual_method) {
        if (count($route_parts) !== count($url_parts)) {
            return false;
        }

        if($route_method != $actual_method) {
            return false;
        }

        for ($i = 0; $i < count($route_parts); $i++) {
            if (preg_match('/^\{.*\}$/', $route_parts[$i])) {
                continue;
            }

            if ($route_parts[$i] !== $url_parts[$i]) {
                return false;
            }
        }

        return true;
    }

    public static function create($uri, $action, $method) {
        $parameters = self::verifyParameters($uri);
        $route = new Route($uri, $action, $method, $parameters);

        array_push(self::$routes, $route);
        return new self;
    }

    public static function get($uri, $action) { return self::create($uri, $action, 'GET'); }

    public static function post($uri, $action) { return self::create($uri, $action, 'POST'); }

    public static function view($uri, $view) {
        $action = function () use($view) {
            return view($view);
        };

        return self::create($uri, $action, 'GET');
    }
    
    public static function name($name) {
        $old_key = array_key_last(self::$routes);
        self::$routes[$name] = self::$routes[$old_key];
        unset(self::$routes[$old_key]);
        return new self;
    }

    public static function route($name, $params = []) {
        $route_found = self::$routes[$name];
        if(!$route_found) {
            throw new Exception("Rota com o nome de {$name} não existe!");
        }
        $uri_parts_obj = explode('/', trim($route_found->uri, '/'));

        $parameters_matched = array();

        foreach ($uri_parts_obj as $key => $value) {
            $key_teste = rtrim(ltrim($value, '{'), '}');
            
            if (strpos($value, '{') !== false && strpos($value, '}') !== false && isset($params[$key_teste])) {
                $parameters_matched[$value] = $params[$key_teste];
            }
        }
        $route_found = self::$routes[$name];

        if(!empty($parameters_matched)) {
            $route_found->parameters = $parameters_matched;
            return $route_found->getUriWithParameters();
        }

        return $route_found->uri;
    }
}