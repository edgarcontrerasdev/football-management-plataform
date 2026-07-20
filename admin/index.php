<?php

require __DIR__.'/../app/bootstrap.php';

Auth::start();
$router = new Router();
$router->route();

?>