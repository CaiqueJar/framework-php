<?php

namespace core;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtensions extends AbstractExtension
{

    public function getFunctions()
    {
        $functions = array();
        $allowed_functions = [
            'dd',
            'dump',
            'redirect',
            'session',
            'asset',
            'route',
            'csrf_token'
        ];

        foreach ($allowed_functions as $defined_function) {
            if (function_exists($defined_function)) {
                $functions[] = new TwigFunction($defined_function, function (...$args) use ($defined_function) {
                    return $defined_function(...$args);
                });
            }
        }
        return $functions;
    }

}
