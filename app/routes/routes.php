<?php

use core\Router;


Router::view('/', 'welcome')->name('welcome');

Router::load();
