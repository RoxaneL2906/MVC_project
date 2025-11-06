<?php

require_once(__DIR__ . "/../core/App.php");

function console(mixed $data): void
{
    ob_start(); 
    var_dump($data);
    $debug_str = ob_get_clean();
    file_put_contents("php://stdout", $debug_str);
}

App::start();

?>

<h1>Hello MVC !</h1>