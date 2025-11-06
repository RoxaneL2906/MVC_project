<?php

require_once(__DIR__."/Router.php");

class App{
    public static function start(){
        $uri = $_SERVER["REQUEST_URI"];

        $uri_elements = explode("/",$uri);

        $controllerName = isset($uri_elements[1])?$uri_elements[1]:"";
        $methodName = isset($uri_elements[2])?$uri_elements[2]:"";
        $params = array_splice($uri_elements,3);

        $controller = Router::getController($controllerName);

        $controller->view($methodName,$params);
    }
}