<?php

namespace core;

class Session {

    public static function save($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function has($key) {
        return isset($_SESSION[$key]) ? true : false;
    }

    public static function delete($key) {
        unset($_SESSION[$key]);
    }
}