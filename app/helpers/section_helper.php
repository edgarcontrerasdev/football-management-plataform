<?php

$GLOBALS['sections'] = [];
$GLOBALS['section_stack'] = [];

function startSection($name)
{
    $GLOBALS['section_stack'][] = $name;
    ob_start();
}

function endSection($name)
{
    if(empty($GLOBALS['section_stack'])){
        throw new Exception("Intentaste cerrar la sección '{$name}' pero no hay ninguna abierta");
    }

    $current = array_pop($GLOBALS['section_stack']);

    if($current !== $name){
        throw new Exception("Sección mal cerrada. Esperaba '{$current}' pero cerraste '{$name}'");
    }

    $content = ob_get_clean();

    if(isset($GLOBALS['sections'][$name])){
        $GLOBALS['sections'][$name] .= $content;
    }else{
        $GLOBALS['sections'][$name] = $content;
    }
}

function section($name, $default = '')
{
    echo $GLOBALS['sections'][$name] ?? $default;
}

function pushOnce($name, $key, $content)
{
    static $loaded = [];

    if (!isset($loaded[$name][$key])) {
        $loaded[$name][$key] = true;
        $GLOBALS['sections'][$name] .= $content;
    }
}

?>