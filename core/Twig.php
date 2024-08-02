<?php

namespace core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig {

    protected $twig;

    public function __construct() {
        $loader = new FilesystemLoader(__DIR__ . '/../app/Views');

        $this->twig = new Environment($loader, [
            'debug' => true,
            'auto_reload' => true,
        ]);
        $this->twig->addExtension(new TwigExtensions());
    }

    public function view(string $view, array $data = []) {
        $template = $this->twig->load($view);
        return $template->render($data);
    }
} 