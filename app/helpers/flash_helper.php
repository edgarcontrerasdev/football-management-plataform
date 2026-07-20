<?php

function flashSuccess($message)
{
    $_SESSION['success'] = $message;
}

function flashError($message)
{
    $_SESSION['error'] = $message;
}

function flashWarning($message)
{
    $_SESSION['warning'] = $message;
}

function flashInfo($message)
{
    $_SESSION['info'] = $message;
}


function flash(){
    $success    = $_SESSION['success']  ?? null;
    $error      = $_SESSION['error']    ?? null;
    $errors     = $_SESSION['errors']   ?? null;
    $warning    = $_SESSION['warning']  ?? null;
    $info       = $_SESSION['info']     ?? null;

    unset(
        $_SESSION['success'],
        $_SESSION['error'],
        $_SESSION['errors'],
        $_SESSION['warning'],
        $_SESSION['info']
    );

    include ROOT_PATH . '/admin/views/components/flash.php';

}