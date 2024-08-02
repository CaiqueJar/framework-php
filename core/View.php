<?php

namespace core;

class View {

    private static function twig() {
        $twig = new Twig;

        return $twig;
    }

    public static function view($view, $data = []) {
        echo self::twig()->view(str_replace('.', '/', $view) . '.html', $data);
    }
}
