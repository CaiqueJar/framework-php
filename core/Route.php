<?php

namespace core;

class Route {
    
    public $uri;
    public $action;
    public $method;
    public $parameters;

    public function __construct($uri, $action, $method, $parameters) {
        $this->uri = $uri;
        $this->action = $action;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getUriWithParameters() {
        $uri = $this->uri;
        foreach ($this->parameters as $key => $value) {
            $uri = str_replace($key, $value, $uri);
        }
        return $uri;
    }
}