<?php

namespace core;

class Redirect {

    private static $url;

    public static function to($location) {
        self::$url = $location;
        return new self();
    }
    
    public static function go() {
        header('Location: ' . self::$url);
        exit();
    }
}