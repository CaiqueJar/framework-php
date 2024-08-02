<?php

namespace core;

use Exception;

class Csrf {

    public static function generate_csrf() {
        if(session()->has('token')) {
            session()->delete('token');
        }

        $token = md5(uniqid());
        session()->save('token', $token);

        return "<input type='hidden' name='token' value='{$token}'>";
    }

    public static function verify_csrf($token) {
        if (!session()->has('token')) {
            throw new Exception('Token Csrf não coincide');
        }

        $token_session = session()->get('token');

        if($token_session != $token) {
            throw new Exception('Token Csrf não coincide');
        }

        // session()->delete('token');
    }
}