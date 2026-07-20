<?php

function view($path, $data = [])
{
    extract($data);

     // quitar extensión si alguien la pone
    $path = str_replace('.php', '', $path);
     // convertir puntos en rutas
    $path = str_replace('.', '/', $path);
    

    $viewPath = ROOT_PATH . '/admin/views/' . $path . '.php';

    if(!file_exists($viewPath)){
        $viewPath = ROOT_PATH.'/admin/views/404.php';
    }

    include ROOT_PATH.'/admin/views/layout.php';
}

?>