<?php

spl_autoload_register(function($class){

    $paths = [
        ROOT_PATH.'/app/controllers/',
        ROOT_PATH.'/app/models/',
        ROOT_PATH.'/app/helpers/',
        ROOT_PATH.'/app/services/'
    ];

    foreach($paths as $basePath){
        $file = findFile($basePath, $class . '.php');

        if($file){
            require_once $file;
            return;
        }
    }

});

/**
 * 🔍 Busca un archivo recursivamente
 */
function findFile($dir, $fileName)
{
    $files = scandir($dir);

    foreach($files as $file){

        if($file === '.' || $file === '..') continue;

        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        // Si es archivo y coincide
        if(is_file($fullPath) && $file === $fileName){
            return $fullPath;
        }

        // Si es carpeta → buscar dentro
        if(is_dir($fullPath)){
            $result = findFile($fullPath, $fileName);
            if($result) return $result;
        }
    }

    return false;
}

?>