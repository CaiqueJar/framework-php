<?php

use core\Csrf;
use core\Redirect;
use core\Router;
use core\Session;
use core\View;

if (!function_exists('dump')) {
    function dump(...$dumps) {
        
        foreach($dumps as $dump) {
            echo '<pre style="background-color: #000; color: #00ff00; margin-bottom: 20px">';
            var_dump($dump);
            echo '</pre>';
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$dumps) {
        dump(...$dumps);
        die();
    }
}

if (!function_exists('redirect')) {
    function redirect() {
        return new Redirect;
    }
}

if (!function_exists('session')) {
    function session() {
        return new Session;
    }
}

if (!function_exists('view')) {
    function view($view) {
        return (new View)->view($view);
    }
}

if(!function_exists('asset')) {
    function asset($asset) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $asset;
    }
}

if(!function_exists('route')) {
    function route($name, $params = []) {
        return (new Router)->route($name, $params);
    }
}

if(!function_exists('csrf_token')) {
    function csrf_token() {
        return Csrf::generate_csrf();
    }
}