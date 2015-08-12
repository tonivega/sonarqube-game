<?php

namespace Privalia\SQHeal\Controller;

class Base {

    protected $controller = null;

    public static function getControllerInstance(\Silex\Application $app){
        $instance = new static($app);
        return $instance->controller;
    }
}


