<?php

return [
    ['type' => 'text', 'field' => 'nombre', 'title' => 'ORGANISMO'],
    ['type' => 'text', 'field' => 'siglas', 'title' => 'SIGLAS'],
    ['type' => 'text', 'field' => 'tipo', 'title' => 'TIPO'],
    ['type' => 'text', 'field' => 'alcance_territorial', 'title' => 'ALCANCE'],
    ['type' => 'status', 'field' => 'estado', 'title' => 'ESTADO', 'source' => 'general'],
    ['type' => 'date', 'field' => 'created_at', 'title' => 'REGISTRADO'],
    ['type' => 'actions', 'title' => 'ACCIONES', 'actions' => [['icon' => 'eye', 'url' => 'index.php?page=organismos&action=detalles&id={id}', 'title' => 'Ver organismo', 'class' => '']]]
];
